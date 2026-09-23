<?php

namespace Tests\Feature;

use App\Models\Receiving;
use App\Models\ReceivingItem;
use App\Models\Role;
use App\Models\StockItem;
use App\Models\StockRequest;
use App\Models\StockRequestItem;
use App\Models\Store;
use App\Models\User;
use App\Services\StockTransactionEngine;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StockTransactionEngineTest extends TestCase
{
    use DatabaseTransactions;

    public function test_receiving_acceptance_increments_stock_quantity_and_creates_ledger()
    {
        $store = Store::first();
        $user = User::first();
        $stock = StockItem::first();

        $initialQuantity = (float) $stock->current_quantity;
        $addQuantity = 50.0;

        $receiving = Receiving::create([
            'btb_number' => 'BTB/TEST/2026/9999',
            'store_id' => $store->id,
            'received_date' => now()->toDateString(),
            'supplier_name' => 'Pembekal Ujian Sdn Bhd',
            'receiving_officer_id' => $user->id,
            'status' => 'ACCEPTED',
        ]);

        ReceivingItem::create([
            'receiving_id' => $receiving->id,
            'stock_item_id' => $stock->id,
            'ordered_quantity' => $addQuantity,
            'delivered_quantity' => $addQuantity,
            'accepted_quantity' => $addQuantity,
            'unit_price' => $stock->unit_price,
            'total_price' => $addQuantity * $stock->unit_price,
            'condition' => 'GOOD',
        ]);

        StockTransactionEngine::processReceivingAcceptance($receiving);

        $stock->refresh();
        $this->assertEquals($initialQuantity + $addQuantity, (float) $stock->current_quantity);
    }

    public function test_issue_stock_deducts_quantity_and_creates_ledger_balance()
    {
        $store = Store::first();
        $user = User::first();
        $stock = StockItem::where('current_quantity', '>=', 10)->first();

        $initialQuantity = (float) $stock->current_quantity;
        $issueQty = 5.0;

        $request = StockRequest::create([
            'request_number' => 'PS8/TEST/2026/9999',
            'form_type' => 'KEW.PS-8',
            'store_id' => $store->id,
            'requester_id' => $user->id,
            'purpose' => 'Ujian Pengeluaran',
            'status' => 'APPROVED',
        ]);

        $item = StockRequestItem::create([
            'stock_request_id' => $request->id,
            'stock_item_id' => $stock->id,
            'requested_quantity' => $issueQty,
            'approved_quantity' => $issueQty,
            'issued_quantity' => $issueQty,
        ]);

        StockTransactionEngine::issueStock($request);

        $stock->refresh();
        $this->assertEquals($initialQuantity - $issueQty, (float) $stock->current_quantity);
    }

    public function test_insufficient_stock_prevents_excessive_issuance()
    {
        $this->expectException(\Exception::class);

        $store = Store::first();
        $user = User::first();
        $stock = StockItem::first();

        // Attempt to issue 100,000 units which exceeds available stock
        $excessiveQty = (float) $stock->current_quantity + 100000;

        $request = StockRequest::create([
            'request_number' => 'PS8/TEST/2026/EXCESS',
            'form_type' => 'KEW.PS-8',
            'store_id' => $store->id,
            'requester_id' => $user->id,
            'purpose' => 'Ujian Lebihan',
            'status' => 'APPROVED',
        ]);

        StockRequestItem::create([
            'stock_request_id' => $request->id,
            'stock_item_id' => $stock->id,
            'requested_quantity' => $excessiveQty,
            'approved_quantity' => $excessiveQty,
            'issued_quantity' => $excessiveQty,
        ]);

        StockTransactionEngine::issueStock($request);
    }
}
