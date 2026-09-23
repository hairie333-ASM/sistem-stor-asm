<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\StockItem;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StockTransferTest extends TestCase
{
    use DatabaseTransactions;

    public function test_inter_store_transfer_deducts_source_and_updates_destination()
    {
        $stores = Store::take(2)->get();
        $sourceStore = $stores[0];
        $destStore = $stores[1];

        $stock = StockItem::where('current_quantity', '>=', 10)->first();
        $initialQty = (float) $stock->current_quantity;
        $transferQty = 4.0;

        $user = User::first();

        // 1. Submit transfer KEW.PS-17
        $transfer = StockTransfer::create([
            'transfer_number' => 'PS17/TEST/2026/001',
            'source_store_id' => $sourceStore->id,
            'destination_store_id' => $destStore->id,
            'requester_id' => $user->id,
            'purpose' => 'Ujian Pindahan Antara Stor',
            'status' => 'SUBMITTED',
        ]);

        $item = StockTransferItem::create([
            'stock_transfer_id' => $transfer->id,
            'stock_item_id' => $stock->id,
            'requested_quantity' => $transferQty,
            'approved_quantity' => $transferQty,
        ]);

        // 2. Approve transfer
        $this->actingAs($user)->post(route('transfers.approve', $transfer), [
            'items' => [
                $item->id => ['approved_quantity' => $transferQty]
            ]
        ]);
        $transfer->refresh();
        $this->assertEquals('APPROVED', $transfer->status);

        // 3. Dispatch transfer (deducts from source store)
        $this->actingAs($user)->post(route('transfers.dispatch', $transfer));
        $transfer->refresh();
        $this->assertEquals('DISPATCHED', $transfer->status);

        $stock->refresh();
        $this->assertEquals($initialQty - $transferQty, (float) $stock->current_quantity);

        // 4. Receive transfer at destination store
        $this->actingAs($user)->post(route('transfers.receive', $transfer), [
            'items' => [
                $item->id => ['received_quantity' => $transferQty]
            ],
            'remarks' => 'Diterima dalam keadaan baik'
        ]);
        $transfer->refresh();
        $this->assertEquals('RECEIVED', $transfer->status);
    }
}
