<?php

namespace Tests\Feature;

use App\Models\LossCase;
use App\Models\LossItem;
use App\Models\Role;
use App\Models\StockItem;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StockLossWriteOffTest extends TestCase
{
    use DatabaseTransactions;

    public function test_loss_case_workflow_and_write_off_execution()
    {
        $store = Store::first();
        $stock = StockItem::where('current_quantity', '>=', 10)->first();
        $initialQty = (float) $stock->current_quantity;
        $lostQty = 2.0;

        $ketuaRole = Role::where('name', 'ketua_jabatan')->first();
        $ketua = User::where('role_id', $ketuaRole->id)->first();

        // 1. Initial report KEW.PS-32
        $lossCase = LossCase::create([
            'case_number' => 'PS32/TEST/2026/001',
            'store_id' => $store->id,
            'incident_date' => now()->toDateString(),
            'discovery_date' => now()->toDateString(),
            'description' => 'Kehilangan semasa pemeriksaan mingguan',
            'total_loss_value' => $lostQty * $stock->unit_price,
            'status' => 'REPORTED',
        ]);

        LossItem::create([
            'loss_case_id' => $lossCase->id,
            'stock_item_id' => $stock->id,
            'quantity' => $lostQty,
            'unit_price' => $stock->unit_price,
            'total_value' => $lostQty * $stock->unit_price,
        ]);

        $this->assertEquals('REPORTED', $lossCase->status);

        // 2. Appoint committee KEW.PS-33
        $this->actingAs($ketua)->post(route('loss.appoint-committee', $lossCase), [
            'investigation_committee_ref' => 'ASM/TEST/KEHILANGAN/01',
            'committees' => [
                ['officer_name' => 'Pegawai A', 'position' => 'Jawatan A', 'department' => 'Unit A', 'role' => 'PENGERUSI'],
                ['officer_name' => 'Pegawai B', 'position' => 'Jawatan B', 'department' => 'Unit B', 'role' => 'AHLI'],
            ]
        ]);
        $lossCase->refresh();
        $this->assertEquals('INVESTIGATING', $lossCase->status);

        // 3. Final report KEW.PS-34
        $this->actingAs($ketua)->post(route('loss.submit-final-report', $lossCase), [
            'findings' => 'Pencerobohan luar dikesan',
            'recommendations' => 'Hapus kira diluluskan dan tambah baik keselamatan',
            'surcharge_recommended' => false,
        ]);
        $lossCase->refresh();
        $this->assertEquals('SUBMITTED_FOR_WRITE_OFF', $lossCase->status);

        // 4. Approve write-off KEW.PS-35
        $this->actingAs($ketua)->post(route('loss.approve-write-off', $lossCase));
        $lossCase->refresh();
        $this->assertEquals('APPROVED', $lossCase->status);
        $this->assertNotNull($lossCase->write_off_cert_number);

        // Stock quantity must have been deducted
        $stock->refresh();
        $this->assertEquals($initialQty - $lostQty, (float) $stock->current_quantity);
    }
}
