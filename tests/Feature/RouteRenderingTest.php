<?php

namespace Tests\Feature;

use App\Models\Disposal;
use App\Models\LossCase;
use App\Models\Receiving;
use App\Models\Role;
use App\Models\StockItem;
use App\Models\StockRequest;
use App\Models\StockTransfer;
use App\Models\StockVerification;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RouteRenderingTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $adminRole = Role::where('name', 'admin')->first();
        $this->admin = User::where('role_id', $adminRole->id)->first();
    }

    public function test_public_and_dashboard_routes_render()
    {
        // Login page
        $response = $this->get(route('login'));
        $response->assertStatus(200);

        // Dashboard & Compliance
        $response = $this->actingAs($this->admin)->get(route('dashboard'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get(route('dashboard.compliance'));
        $response->assertStatus(200);
    }

    public function test_stock_catalog_and_kew_ps_views_render()
    {
        $stock = StockItem::first();

        // Stock Index
        $response = $this->actingAs($this->admin)->get(route('stock.index'));
        $response->assertStatus(200);

        // KEW.PS-3 Digital Stock Register
        $response = $this->actingAs($this->admin)->get(route('stock.show', $stock));
        $response->assertStatus(200);

        // KEW.PS-4 Kad Petak
        $response = $this->actingAs($this->admin)->get(route('stock.kad-petak', $stock));
        $response->assertStatus(200);

        // KEW.PS-5 Analisis Kumpulan A & B
        $response = $this->actingAs($this->admin)->get(route('stock.group-ab'));
        $response->assertStatus(200);

        // KEW.PS-6 Pemantauan Tarikh Luput
        $response = $this->actingAs($this->admin)->get(route('stock.expiry'));
        $response->assertStatus(200);

        // Master Register, Detail & Catalogue Print
        $response = $this->actingAs($this->admin)->get(route('stock-register.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get(route('stock-register.show', $stock));
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get(route('stock-register.print', $stock));
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get(route('stock.print-catalogue'));
        $response->assertStatus(200);
    }

    public function test_receiving_and_requests_views_render()
    {
        $receiving = Receiving::first();
        $request = StockRequest::first();

        // Receiving Index & Detail (KEW.PS-1)
        $response = $this->actingAs($this->admin)->get(route('receiving.index'));
        $response->assertStatus(200);

        if ($receiving) {
            $response = $this->actingAs($this->admin)->get(route('receiving.show', $receiving));
            $response->assertStatus(200);

            $response = $this->actingAs($this->admin)->get(route('receiving.print-btb', $receiving));
            $response->assertStatus(200);
        }

        // Requests Index & Detail (KEW.PS-8)
        $response = $this->actingAs($this->admin)->get(route('requests.index'));
        $response->assertStatus(200);

        if ($request) {
            $response = $this->actingAs($this->admin)->get(route('requests.show', $request));
            $response->assertStatus(200);

            $response = $this->actingAs($this->admin)->get(route('requests.print-ps8', $request));
            $response->assertStatus(200);
        }
    }

    public function test_verification_disposal_loss_views_render()
    {
        $verification = StockVerification::first();
        $disposal = Disposal::first();
        $loss = LossCase::first();

        // Verification (AM 6.6)
        $response = $this->actingAs($this->admin)->get(route('verification.index'));
        $response->assertStatus(200);

        if ($verification) {
            $response = $this->actingAs($this->admin)->get(route('verification.show', $verification));
            $response->assertStatus(200);
        }

        // Disposal (AM 6.9)
        $response = $this->actingAs($this->admin)->get(route('disposal.index'));
        $response->assertStatus(200);

        if ($disposal) {
            $response = $this->actingAs($this->admin)->get(route('disposal.show', $disposal));
            $response->assertStatus(200);
        }

        // Loss & Write-off (AM 6.10)
        $response = $this->actingAs($this->admin)->get(route('loss.index'));
        $response->assertStatus(200);

        if ($loss) {
            $response = $this->actingAs($this->admin)->get(route('loss.show', $loss));
            $response->assertStatus(200);
        }
    }

    public function test_reports_and_admin_views_render()
    {
        // Reports Hub
        $response = $this->actingAs($this->admin)->get(route('reports.index'));
        $response->assertStatus(200);

        // KEW.PS-14 Kadar Pusingan Stok
        $response = $this->actingAs($this->admin)->get(route('reports.kew-ps-14'));
        $response->assertStatus(200);

        // Penilaian Stok
        $response = $this->actingAs($this->admin)->get(route('reports.stock-valuation'));
        $response->assertStatus(200);

        // Laporan Menokok Stok
        $response = $this->actingAs($this->admin)->get(route('reports.reorder'));
        $response->assertStatus(200);

        // Admin Management
        $response = $this->actingAs($this->admin)->get(route('admin.users'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get(route('admin.settings'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get(route('admin.audit'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get(route('admin.year-end'));
        $response->assertStatus(200);
    }
}
