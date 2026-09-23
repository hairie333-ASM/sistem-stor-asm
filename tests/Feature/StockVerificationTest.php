<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\StockItem;
use App\Models\StockVerification;
use App\Models\Store;
use App\Models\User;
use App\Models\VerificationItem;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StockVerificationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_verification_lifecycle_from_start_to_certificate_issuance()
    {
        $store = Store::first();
        $verifiers = User::take(2)->get();
        $ketuaRole = Role::where('name', 'ketua_jabatan')->first();
        $ketua = User::where('role_id', $ketuaRole->id)->first();

        // 1. Create scheduled verification
        $verification = StockVerification::create([
            'verification_number' => 'PS11/TEST/2026/001',
            'store_id' => $store->id,
            'year' => 2026,
            'appointment_letter_ref' => 'ASM/TEST/LANTIKAN/01',
            'scheduled_date' => now()->toDateString(),
            'verifier_1_id' => $verifiers[0]->id,
            'verifier_2_id' => $verifiers[1]->id,
            'status' => 'SCHEDULED',
            'is_frozen' => false,
        ]);

        $this->assertEquals('SCHEDULED', $verification->status);

        // 2. Start verification (freezes store and takes snapshot)
        $this->actingAs($ketua)->post(route('verification.start', $verification));
        $verification->refresh();

        $this->assertEquals('IN_PROGRESS', $verification->status);
        $this->assertTrue((bool) $verification->is_frozen);
        $this->assertGreaterThan(0, $verification->items()->count());

        // 3. Record physical counts
        $item = $verification->items()->first();
        $this->actingAs($verifiers[0])->post(route('verification.record-counts', $verification), [
            'items' => [
                $item->id => [
                    'physical_quantity' => $item->system_quantity,
                    'condition_status' => 'BAIK',
                    'remarks' => 'Semakan fizikal tepat',
                ]
            ],
            'findings_summary' => 'Semua dalam keadaan teratur',
            'corrective_actions' => 'Tiada tindakan diperlukan',
        ]);

        $verification->refresh();
        $this->assertEquals('COMPLETED', $verification->status);
        $this->assertFalse((bool) $verification->is_frozen);
        $this->assertNotNull($verification->report_number);

        // 4. Ketua Jabatan approval & issuance of KEW.PS-13 certificate
        $this->actingAs($ketua)->post(route('verification.approve', $verification));
        $verification->refresh();

        $this->assertEquals('APPROVED', $verification->status);
        $this->assertNotNull($verification->cert_number);
    }
}
