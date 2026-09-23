<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\StockRequest;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SeparationOfDutiesTest extends TestCase
{
    use DatabaseTransactions;

    public function test_requester_cannot_approve_own_stock_request()
    {
        $pemohonRole = Role::where('name', 'pemohon')->first();
        $user = User::where('role_id', $pemohonRole->id)->first();
        $store = Store::first();

        $request = StockRequest::create([
            'request_number' => 'PS8/TEST/SELF/001',
            'form_type' => 'KEW.PS-8',
            'store_id' => $store->id,
            'requester_id' => $user->id,
            'purpose' => 'Ujian Self Approval',
            'status' => 'SUBMITTED',
        ]);

        // Acting as the requester attempting to self-approve
        $response = $this->actingAs($user)->post(route('requests.approve', $request), [
            'items' => []
        ]);

        // Must redirect with error due to separation of duties
        $response->assertSessionHas('error');
        $this->assertEquals('SUBMITTED', $request->fresh()->status);
    }
}
