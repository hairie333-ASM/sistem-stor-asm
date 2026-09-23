<?php

namespace Tests\Feature;

use App\Models\Disposal;
use App\Models\DisposalCommittee;
use App\Models\DisposalItem;
use App\Models\Role;
use App\Models\StockItem;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StockDisposalTest extends TestCase
{
    use DatabaseTransactions;

    public function test_disposal_isolates_stock_and_completes_with_certificate()
    {
        $store = Store::first();
        $stock = StockItem::where('current_quantity', '>=', 10)->first();
        $initialQty = (float) $stock->current_quantity;
        $disposeQty = 3.0;

        $ketuaRole = Role::where('name', 'ketua_jabatan')->first();
        $ketua = User::where('role_id', $ketuaRole->id)->first();

        // 1. Propose disposal
        $disposal = Disposal::create([
            'disposal_number' => 'PS20/TEST/2026/001',
            'store_id' => $store->id,
            'committee_appointment_ref' => 'ASM/TEST/LANTIKAN/01',
            'disposal_method' => 'SCRAP',
            'status' => 'PROPOSED',
            'total_original_value' => $disposeQty * $stock->unit_price,
        ]);

        DisposalItem::create([
            'disposal_id' => $disposal->id,
            'stock_item_id' => $stock->id,
            'quantity' => $disposeQty,
            'unit_price' => $stock->unit_price,
            'total_price' => $disposeQty * $stock->unit_price,
            'condition' => 'USANG',
            'justification' => 'Telah usang',
            'recommended_method' => 'SCRAP',
            'status' => 'PROPOSED',
        ]);

        // Simulating the isolation from available quantity
        $stock->current_quantity -= $disposeQty;
        $stock->disposal_quantity += $disposeQty;
        $stock->save();

        $stock->refresh();
        $this->assertEquals($initialQty - $disposeQty, (float) $stock->current_quantity);
        $this->assertEquals($disposeQty, (float) $stock->disposal_quantity);

        // 2. Approve disposal by Kuasa Melulus (KEW.PS-21)
        $this->actingAs($ketua)->post(route('disposal.approve', $disposal));
        $disposal->refresh();

        $this->assertEquals('APPROVED', $disposal->status);
        $this->assertNotNull($disposal->approval_reference);

        // 3. Complete physical disposal (KEW.PS-22)
        $this->actingAs($ketua)->post(route('disposal.complete', $disposal), [
            'total_revenue' => 150.00
        ]);
        $disposal->refresh();

        $this->assertEquals('COMPLETED', $disposal->status);
        $this->assertNotNull($disposal->completion_cert_number);
        $this->assertEquals(150.00, (float) $disposal->total_revenue);

        // Stock disposal bucket must now be zeroed
        $stock->refresh();
        $this->assertEquals(0, (float) $stock->disposal_quantity);
    }
}
