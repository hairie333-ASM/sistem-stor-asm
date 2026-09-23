<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\StockCategory;
use App\Models\StockItem;
use App\Models\StockRequest;
use App\Models\Store;
use App\Models\User;
use Tests\TestCase;

class StockRequestSearchTest extends TestCase
{
    protected $pemohon;
    protected $store;
    protected $stock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pemohon = User::whereHas('role', fn($q) => $q->where('name', 'pemohon'))->first();
        if (!$this->pemohon) {
            $role = Role::firstOrCreate(['name' => 'pemohon'], ['description' => 'Pemohon']);
            $this->pemohon = User::create([
                'name' => 'Ujian Pemohon',
                'email' => 'pemohon.test@asm.gov.my',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
                'department' => 'Program & Outreach',
                'is_active' => true,
            ]);
        }

        $this->store = Store::first();
        $this->stock = StockItem::where('status', 'AVAILABLE')->where('current_quantity', '>', 0)->first();
    }

    public function test_request_create_page_contains_search_features()
    {
        $response = $this->actingAs($this->pemohon)->get(route('requests.create'));

        $response->assertStatus(200);
        $response->assertSee('Carian Katalog Pintar', false);
        $response->assertSee('Taip kod, perihal stok', false);
        $response->assertSee('Carian Katalog Pintar Stok ASM', false);
        $response->assertSee('filteredStocks', false);
        $response->assertSee('chooseFromModal', false);
    }

    public function test_request_create_preselects_given_stock_id()
    {
        $response = $this->actingAs($this->pemohon)->get(route('requests.create', ['stock_id' => $this->stock->id]));

        $response->assertStatus(200);
        $response->assertSee((string)$this->stock->id);
    }

    public function test_submitting_stock_request_from_search_selection_succeeds()
    {
        $response = $this->actingAs($this->pemohon)->post(route('requests.store'), [
            'form_type' => 'KEW.PS-8',
            'store_id' => $this->store->id,
            'department' => 'Program & Outreach ASM',
            'priority' => 'NORMAL',
            'purpose' => 'Ujian permohonan dengan carian stok pintar',
            'items' => [
                [
                    'stock_item_id' => $this->stock->id,
                    'requested_quantity' => 2,
                    'remarks' => 'Ditemui melalui carian pintar',
                ]
            ],
        ]);

        $response->assertRedirect(route('requests.index'));

        $this->assertDatabaseHas('stock_requests', [
            'requester_id' => $this->pemohon->id,
            'store_id' => $this->store->id,
            'form_type' => 'KEW.PS-8',
            'purpose' => 'Ujian permohonan dengan carian stok pintar',
        ]);

        $this->assertDatabaseHas('stock_request_items', [
            'stock_item_id' => $this->stock->id,
            'requested_quantity' => 2,
        ]);
    }

    public function test_search_api_returns_matching_stocks()
    {
        $response = $this->actingAs($this->pemohon)->getJson(route('stock.search-api', [
            'q' => substr($this->stock->description, 0, 5),
        ]));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $this->stock->id,
        ]);
    }
}

