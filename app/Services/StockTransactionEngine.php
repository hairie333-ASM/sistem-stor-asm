<?php

namespace App\Services;

use App\Models\Receiving;
use App\Models\ReceivingItem;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\StockBatch;
use App\Models\StockItem;
use App\Models\StockRequest;
use App\Models\StockRequestItem;
use App\Models\StockReturn;
use App\Models\StockReturnItem;
use App\Models\StockTransaction;
use App\Models\StockTransactionItem;
use App\Models\StockTransfer;
use App\Models\Disposal;
use App\Models\DisposalItem;
use App\Models\LossCase;
use App\Models\LossItem;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockTransactionEngine
{
    /**
     * Process receiving of items after physical inspection (KEW.PS-1 BTB)
     */
    public static function processReceivingAcceptance(Receiving $receiving): void
    {
        DB::transaction(function () use ($receiving) {
            $userId = Auth::id() ?? $receiving->receiving_officer_id ?? 1;
            $txnNumber = DocumentNumberService::generate('TXN');

            $transaction = StockTransaction::create([
                'transaction_number' => $txnNumber,
                'transaction_type' => 'RECEIPT',
                'kew_ps_type' => 'KEW.PS-1',
                'reference_number' => $receiving->btb_number,
                'store_id' => $receiving->store_id,
                'user_id' => $userId,
                'party_name' => $receiving->supplier_name,
                'transaction_date' => $receiving->delivery_order_date ?? now()->toDateString(),
                'notes' => 'Terimaan Barang KEW.PS-1 (DO: ' . $receiving->delivery_order_number . ')',
            ]);

            foreach ($receiving->items as $item) {
                if ($item->accepted_quantity <= 0) {
                    continue;
                }

                $stock = StockItem::lockForUpdate()->find($item->stock_item_id);
                $qty = (float) $item->accepted_quantity;
                $unitPrice = (float) $item->unit_price;
                $totalPrice = $qty * $unitPrice;

                $balanceBefore = (float) $stock->current_quantity;
                $balanceAfter = $balanceBefore + $qty;
                $balanceValueAfter = $balanceAfter * ($stock->unit_price > 0 ? $stock->unit_price : $unitPrice);

                // Update Stock Item quantity and current unit price
                $stock->current_quantity = $balanceAfter;
                if ($unitPrice > 0) {
                    $stock->unit_price = $unitPrice;
                }
                $stock->save();

                // Batch creation if applicable
                $batchId = null;
                if ($item->batch_number || $item->expiry_date || $stock->is_batch_controlled || $stock->is_expiry_controlled) {
                    $batch = StockBatch::create([
                        'stock_item_id' => $stock->id,
                        'batch_number' => $item->batch_number ?? 'BCH-' . date('Ymd') . '-' . rand(100, 999),
                        'expiry_date' => $item->expiry_date,
                        'quantity' => $qty,
                        'remaining_quantity' => $qty,
                        'unit_price' => $unitPrice,
                        'location_id' => $item->location_id ?? $stock->default_location_id,
                        'status' => 'AVAILABLE',
                    ]);
                    $batchId = $batch->id;
                }

                // Record Ledger Item
                StockTransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'stock_item_id' => $stock->id,
                    'batch_id' => $batchId,
                    'location_id' => $item->location_id ?? $stock->default_location_id,
                    'movement_type' => 'IN',
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'balance_quantity_before' => $balanceBefore,
                    'balance_quantity_after' => $balanceAfter,
                    'balance_value_after' => $balanceValueAfter,
                    'remarks' => 'Penerimaan Stok BTB: ' . $receiving->btb_number,
                ]);
            }

            AuditLogService::log(
                'RECEIVE',
                'Receiving',
                (string) $receiving->id,
                null,
                ['btb_number' => $receiving->btb_number, 'supplier' => $receiving->supplier_name],
                'Penerimaan stok berjaya diproses dan direkod ke dalam Daftar Stok KEW.PS-3.'
            );
        });
    }

    /**
     * Reserve stock when a requisition is approved by Pegawai Pelulus
     */
    public static function reserveStockForRequest(StockRequest $request): void
    {
        DB::transaction(function () use ($request) {
            foreach ($request->items as $item) {
                $stock = StockItem::lockForUpdate()->find($item->stock_item_id);
                $approvedQty = (float) $item->approved_quantity;

                if ($stock->current_quantity < $approvedQty) {
                    throw new Exception("Baki stok tidak mencukupi untuk item: {$stock->description}. Baki semasa: {$stock->current_quantity}, kuantiti diluluskan: {$approvedQty}");
                }

                // Move from available to reserved
                $stock->current_quantity -= $approvedQty;
                $stock->reserved_quantity += $approvedQty;
                $stock->save();
            }

            AuditLogService::log(
                'RESERVE',
                'StockRequest',
                (string) $request->id,
                null,
                ['request_number' => $request->request_number, 'status' => $request->status],
                'Kuantiti stok diperuntukkan (reserved) untuk permohonan ' . $request->request_number
            );
        });
    }

    /**
     * Issue stock to recipient (KEW.PS-8 / KEW.PS-7)
     */
    public static function issueStock(StockRequest $request): void
    {
        DB::transaction(function () use ($request) {
            $userId = Auth::id() ?? $request->issuer_id ?? $request->requester_id ?? 1;
            $txnNumber = DocumentNumberService::generate('TXN');

            $transaction = StockTransaction::create([
                'transaction_number' => $txnNumber,
                'transaction_type' => 'ISSUE',
                'kew_ps_type' => $request->form_type,
                'reference_number' => $request->request_number,
                'store_id' => $request->store_id,
                'user_id' => $userId,
                'party_name' => $request->requester->name . ' (' . ($request->department ?? 'Unit') . ')',
                'transaction_date' => now()->toDateString(),
                'notes' => 'Pengeluaran Stok ' . $request->form_type . ' untuk: ' . $request->purpose,
            ]);

            foreach ($request->items as $item) {
                $issuedQty = (float) $item->issued_quantity;
                if ($issuedQty <= 0) {
                    continue;
                }

                $stock = StockItem::lockForUpdate()->find($item->stock_item_id);

                // Deduct from reserved (or available if direct issue)
                if ($stock->reserved_quantity >= $issuedQty) {
                    $stock->reserved_quantity -= $issuedQty;
                } else {
                    $remainder = $issuedQty - (float) $stock->reserved_quantity;
                    $stock->reserved_quantity = 0;
                    if ($stock->current_quantity < $remainder) {
                        throw new Exception("Baki stok tidak mencukupi untuk item: {$stock->description}");
                    }
                    $stock->current_quantity -= $remainder;
                }
                $stock->save();

                $balanceBefore = (float) $stock->current_quantity + $issuedQty;
                $balanceAfter = (float) $stock->current_quantity;
                $unitPrice = (float) $stock->unit_price;
                $totalPrice = $issuedQty * $unitPrice;
                $balanceValueAfter = $balanceAfter * $unitPrice;

                // Handle FIFO / MDKD batch deduction if batch exists
                $batchId = null;
                if ($stock->is_batch_controlled || $stock->is_expiry_controlled) {
                    $oldestBatch = StockBatch::where('stock_item_id', $stock->id)
                        ->where('remaining_quantity', '>', 0)
                        ->orderByRaw('CASE WHEN expiry_date IS NOT NULL THEN expiry_date ELSE created_at END ASC')
                        ->lockForUpdate()
                        ->first();

                    if ($oldestBatch) {
                        $batchId = $oldestBatch->id;
                        $oldestBatch->remaining_quantity = max(0, $oldestBatch->remaining_quantity - $issuedQty);
                        $oldestBatch->save();
                    }
                }

                // Record Ledger Item
                StockTransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'stock_item_id' => $stock->id,
                    'batch_id' => $batchId,
                    'location_id' => $stock->default_location_id,
                    'movement_type' => 'OUT',
                    'quantity' => $issuedQty,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'balance_quantity_before' => $balanceBefore,
                    'balance_quantity_after' => $balanceAfter,
                    'balance_value_after' => $balanceValueAfter,
                    'remarks' => 'Pengeluaran Stok ' . $request->form_type . ': ' . $request->request_number,
                ]);
            }

            AuditLogService::log(
                'ISSUE',
                'StockRequest',
                (string) $request->id,
                null,
                ['request_number' => $request->request_number],
                'Pengeluaran stok direkodkan dan Daftar Stok KEW.PS-3 dikemaskini.'
            );
        });
    }

    /**
     * Process stock return after condition assessment
     */
    public static function processReturn(StockReturn $return): void
    {
        DB::transaction(function () use ($return) {
            $userId = Auth::id() ?? $return->inspector_id ?? $return->user_id ?? 1;
            $txnNumber = DocumentNumberService::generate('TXN');

            $hasGoodStock = false;
            foreach ($return->items as $item) {
                if ($item->condition === 'GOOD' && $item->accepted_quantity > 0) {
                    $hasGoodStock = true;
                    break;
                }
            }

            if ($hasGoodStock) {
                $transaction = StockTransaction::create([
                    'transaction_number' => $txnNumber,
                    'transaction_type' => 'RETURN',
                    'kew_ps_type' => 'PULANG',
                    'reference_number' => $return->return_number,
                    'store_id' => $return->store_id,
                    'user_id' => $userId,
                    'party_name' => $return->user->name,
                    'transaction_date' => now()->toDateString(),
                    'notes' => 'Pemulangan Stok (Ref: ' . $return->return_number . ')',
                ]);

                foreach ($return->items as $item) {
                    $stock = StockItem::lockForUpdate()->find($item->stock_item_id);
                    $qty = (float) $item->accepted_quantity;

                    if ($item->condition === 'GOOD' && $qty > 0) {
                        $balanceBefore = (float) $stock->current_quantity;
                        $balanceAfter = $balanceBefore + $qty;
                        $stock->current_quantity = $balanceAfter;
                        $stock->save();

                        $item->is_restocked = true;
                        $item->save();

                        StockTransactionItem::create([
                            'transaction_id' => $transaction->id,
                            'stock_item_id' => $stock->id,
                            'movement_type' => 'IN',
                            'quantity' => $qty,
                            'unit_price' => $stock->unit_price,
                            'total_price' => $qty * $stock->unit_price,
                            'balance_quantity_before' => $balanceBefore,
                            'balance_quantity_after' => $balanceAfter,
                            'balance_value_after' => $balanceAfter * $stock->unit_price,
                            'remarks' => 'Pemulangan stok dalam keadaan baik: ' . $return->return_number,
                        ]);
                    } elseif ($item->condition === 'DAMAGED' && $qty > 0) {
                        // Put in damaged_quantity, do not add to available
                        $stock->damaged_quantity += $qty;
                        $stock->save();
                    }
                }
            }

            AuditLogService::log(
                'RETURN',
                'StockReturn',
                (string) $return->id,
                null,
                ['return_number' => $return->return_number],
                'Pemulangan stok dinilai dan dikemaskini.'
            );
        });
    }

    /**
     * Dispatch stock for inter-store transfer (KEW.PS-17)
     */
    public static function dispatchTransfer(StockTransfer $transfer): void
    {
        DB::transaction(function () use ($transfer) {
            $userId = Auth::id() ?? $transfer->sender_id ?? $transfer->requester_id ?? 1;
            $txnNumber = DocumentNumberService::generate('TXN');

            $transaction = StockTransaction::create([
                'transaction_number' => $txnNumber,
                'transaction_type' => 'TRANSFER_OUT',
                'kew_ps_type' => 'KEW.PS-17',
                'reference_number' => $transfer->transfer_number,
                'store_id' => $transfer->source_store_id,
                'user_id' => $userId,
                'party_name' => $transfer->destinationStore->name,
                'transaction_date' => now()->toDateString(),
                'notes' => 'Pindahan Stok Keluar KEW.PS-17 ke ' . $transfer->destinationStore->name . ' untuk: ' . $transfer->purpose,
            ]);

            foreach ($transfer->items as $item) {
                $qty = (float) $item->approved_quantity;
                if ($qty <= 0) continue;

                $stock = StockItem::lockForUpdate()->find($item->stock_item_id);
                if ($stock->current_quantity < $qty) {
                    throw new \Exception("Baki stok tidak mencukupi untuk penghantaran pindahan {$stock->stock_code}.");
                }

                $previousQty = (float) $stock->current_quantity;

                $stock->current_quantity -= $qty;
                $stock->save();

                $balanceValueAfter = (float) ($stock->current_quantity * $stock->unit_price);

                StockTransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'stock_item_id' => $stock->id,
                    'movement_type' => 'OUT',
                    'quantity' => $qty,
                    'unit_price' => $stock->unit_price,
                    'total_price' => $qty * $stock->unit_price,
                    'balance_quantity_before' => $previousQty,
                    'balance_quantity_after' => $stock->current_quantity,
                    'balance_value_after' => $balanceValueAfter,
                    'remarks' => 'Pindahan Stok Keluar KEW.PS-17: ' . $transfer->transfer_number,
                ]);

                $item->sent_quantity = $qty;
                $item->save();
            }

            AuditLogService::log(
                'DISPATCH_TRANSFER',
                'StockTransfer',
                (string) $transfer->id,
                null,
                ['status' => 'DISPATCHED'],
                'Kuantiti stok ditolak keluar dari stor pembekal untuk pindahan.'
            );
        });
    }

    /**
     * Receive transfer at destination store (KEW.PS-17)
     */
    public static function receiveTransfer(StockTransfer $transfer): void
    {
        DB::transaction(function () use ($transfer) {
            $userId = Auth::id() ?? $transfer->receiver_id ?? $transfer->requester_id ?? 1;
            $txnNumber = DocumentNumberService::generate('TXN');

            $transaction = StockTransaction::create([
                'transaction_number' => $txnNumber,
                'transaction_type' => 'TRANSFER_IN',
                'kew_ps_type' => 'KEW.PS-17',
                'reference_number' => $transfer->transfer_number,
                'store_id' => $transfer->destination_store_id,
                'destination_store_id' => $transfer->source_store_id,
                'user_id' => $userId,
                'party_name' => $transfer->sourceStore->name,
                'transaction_date' => now()->toDateString(),
                'notes' => 'Pindahan Masuk daripada ' . $transfer->sourceStore->name,
            ]);

            foreach ($transfer->items as $item) {
                $qty = (float) ($item->received_quantity > 0 ? $item->received_quantity : $item->sent_quantity);
                if ($qty <= 0) continue;

                $stock = StockItem::lockForUpdate()->find($item->stock_item_id);

                $balanceBefore = (float) $stock->current_quantity;
                $balanceAfter = $balanceBefore + $qty;
                $stock->current_quantity = $balanceAfter;
                $stock->save();

                $item->received_quantity = $qty;
                $item->save();

                StockTransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'stock_item_id' => $stock->id,
                    'movement_type' => 'IN',
                    'quantity' => $qty,
                    'unit_price' => $stock->unit_price,
                    'total_price' => $qty * $stock->unit_price,
                    'balance_quantity_before' => $balanceBefore,
                    'balance_quantity_after' => $balanceAfter,
                    'balance_value_after' => $balanceAfter * $stock->unit_price,
                    'remarks' => 'Pindahan Masuk disahkan KEW.PS-17: ' . $transfer->transfer_number,
                ]);
            }

            AuditLogService::log(
                'TRANSFER_IN',
                'StockTransfer',
                (string) $transfer->id,
                null,
                ['transfer_number' => $transfer->transfer_number],
                'Penerimaan pindahan stok disahkan dan direkodkan ke stor penerima.'
            );
        });
    }

    /**
     * Apply approved stock adjustment (KEW.PS-15 / KEW.PS-16)
     */
    public static function applyAdjustment(StockAdjustment $adjustment): void
    {
        DB::transaction(function () use ($adjustment) {
            $userId = Auth::id() ?? $adjustment->approver_id ?? 1;
            $txnNumber = DocumentNumberService::generate('TXN');

            $transaction = StockTransaction::create([
                'transaction_number' => $txnNumber,
                'transaction_type' => 'ADJUSTMENT',
                'kew_ps_type' => 'KEW.PS-15',
                'reference_number' => $adjustment->adjustment_number,
                'store_id' => $adjustment->store_id,
                'user_id' => $userId,
                'party_name' => 'Kelulusan Pelarasan (' . $adjustment->perakuan_number . ')',
                'transaction_date' => now()->toDateString(),
                'notes' => 'Pelarasan Stok KEW.PS-15: Sebab - ' . $adjustment->reason,
            ]);

            foreach ($adjustment->items as $item) {
                $stock = StockItem::lockForUpdate()->find($item->stock_item_id);
                $adjQty = (float) $item->adjustment_quantity;

                $balanceBefore = (float) $stock->current_quantity;
                $balanceAfter = $balanceBefore + $adjQty;

                if ($balanceAfter < 0) {
                    throw new Exception("Pelarasan menyebabkan baki stok negatif untuk item: {$stock->description}");
                }

                $stock->current_quantity = $balanceAfter;
                $stock->save();

                StockTransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'stock_item_id' => $stock->id,
                    'movement_type' => $adjQty >= 0 ? 'IN' : 'OUT',
                    'quantity' => abs($adjQty),
                    'unit_price' => $stock->unit_price,
                    'total_price' => abs($adjQty) * $stock->unit_price,
                    'balance_quantity_before' => $balanceBefore,
                    'balance_quantity_after' => $balanceAfter,
                    'balance_value_after' => $balanceAfter * $stock->unit_price,
                    'remarks' => 'Pelarasan Stok KEW.PS-15 (' . ($adjQty >= 0 ? '+' : '') . $adjQty . '): ' . $item->reason_detail,
                ]);
            }

            AuditLogService::log(
                'ADJUST',
                'StockAdjustment',
                (string) $adjustment->id,
                null,
                ['adjustment_number' => $adjustment->adjustment_number],
                'Pelarasan stok diluluskan dan baki fizikal dikemaskini.'
            );
        });
    }

    /**
     * Complete stock disposal (KEW.PS-20 / KEW.PS-23)
     */
    public static function completeDisposal(Disposal $disposal): void
    {
        DB::transaction(function () use ($disposal) {
            $userId = Auth::id() ?? $disposal->approver_id ?? 1;
            $txnNumber = DocumentNumberService::generate('TXN');

            $transaction = StockTransaction::create([
                'transaction_number' => $txnNumber,
                'transaction_type' => 'DISPOSAL',
                'kew_ps_type' => 'KEW.PS-20',
                'reference_number' => $disposal->disposal_number,
                'store_id' => $disposal->store_id,
                'user_id' => $userId,
                'party_name' => 'Lembaga Pelupusan (Sijil: ' . $disposal->completion_cert_number . ')',
                'transaction_date' => now()->toDateString(),
                'notes' => 'Pelupusan Stok mengikut Kaedah: ' . $disposal->disposal_method,
            ]);

            foreach ($disposal->items as $item) {
                $stock = StockItem::lockForUpdate()->find($item->stock_item_id);
                $qty = (float) $item->quantity;

                // Move from disposal_quantity or current_quantity
                if ($stock->disposal_quantity >= $qty) {
                    $stock->disposal_quantity -= $qty;
                } else {
                    $stock->current_quantity = max(0, $stock->current_quantity - $qty);
                }
                $stock->save();

                $balanceBefore = (float) $stock->current_quantity + $qty;
                $balanceAfter = (float) $stock->current_quantity;

                StockTransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'stock_item_id' => $stock->id,
                    'movement_type' => 'OUT',
                    'quantity' => $qty,
                    'unit_price' => $stock->unit_price,
                    'total_price' => $qty * $stock->unit_price,
                    'balance_quantity_before' => $balanceBefore,
                    'balance_quantity_after' => $balanceAfter,
                    'balance_value_after' => $balanceAfter * $stock->unit_price,
                    'remarks' => 'Pelupusan Stok Diluluskan: ' . $disposal->completion_cert_number,
                ]);
            }

            AuditLogService::log(
                'DISPOSE',
                'Disposal',
                (string) $disposal->id,
                null,
                ['disposal_number' => $disposal->disposal_number],
                'Pelupusan stok selesai dan dikeluarkan daripada daftar stok.'
            );
        });
    }

    /**
     * Apply approved stock write-off (KEW.PS-35)
     */
    public static function completeWriteOff(LossCase $lossCase): void
    {
        DB::transaction(function () use ($lossCase) {
            $userId = Auth::id() ?? $lossCase->approver_id ?? 1;
            $txnNumber = DocumentNumberService::generate('TXN');

            $transaction = StockTransaction::create([
                'transaction_number' => $txnNumber,
                'transaction_type' => 'WRITE_OFF',
                'kew_ps_type' => 'KEW.PS-35',
                'reference_number' => $lossCase->case_number,
                'store_id' => $lossCase->store_id,
                'user_id' => $userId,
                'party_name' => 'Kuasa Melulus Hapus Kira (Sijil: ' . $lossCase->write_off_cert_number . ')',
                'transaction_date' => now()->toDateString(),
                'notes' => 'Hapus Kira Kehilangan Stok KEW.PS-35: ' . $lossCase->description,
            ]);

            foreach ($lossCase->items as $item) {
                $stock = StockItem::lockForUpdate()->find($item->stock_item_id);
                $qty = (float) $item->quantity;

                $balanceBefore = (float) $stock->current_quantity;
                $balanceAfter = max(0, $balanceBefore - $qty);

                $stock->current_quantity = $balanceAfter;
                $stock->written_off_quantity += $qty;
                $stock->save();

                StockTransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'stock_item_id' => $stock->id,
                    'movement_type' => 'OUT',
                    'quantity' => $qty,
                    'unit_price' => $stock->unit_price,
                    'total_price' => $qty * $stock->unit_price,
                    'balance_quantity_before' => $balanceBefore,
                    'balance_quantity_after' => $balanceAfter,
                    'balance_value_after' => $balanceAfter * $stock->unit_price,
                    'remarks' => 'Hapus Kira Kehilangan Stok: Sijil ' . $lossCase->write_off_cert_number,
                ]);
            }

            AuditLogService::log(
                'WRITE_OFF',
                'LossCase',
                (string) $lossCase->id,
                null,
                ['case_number' => $lossCase->case_number],
                'Hapus kira kehilangan stok diluluskan dan baki stok dikemaskini.'
            );
        });
    }
}
