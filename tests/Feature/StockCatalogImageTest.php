<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\StockItem;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class StockCatalogImageTest extends TestCase
{
    protected $admin;
    protected $itemWithImage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first();
        if (!$this->admin) {
            $role = Role::firstOrCreate(['name' => 'admin'], ['description' => 'System Administrator']);
            $this->admin = User::create([
                'name' => 'Admin Test',
                'email' => 'admin.test@asm.gov.my',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
                'is_active' => true,
            ]);
        }

        $this->itemWithImage = StockItem::whereNotNull('image_url')->first();
    }

    public function test_stock_model_image_accessors()
    {
        $this->assertNotNull($this->itemWithImage, 'Setidaknya satu item stok mesti mempunyai image_url');
        
        $this->assertTrue($this->itemWithImage->has_catalog_image);
        $this->assertNotEmpty($this->itemWithImage->display_image);
        $this->assertStringContainsString('images/stocks/', $this->itemWithImage->image_url);
        $this->assertNotEmpty($this->itemWithImage->catalog_code);
    }

    public function test_stock_index_has_gallery_view_and_photo_filter()
    {
        $response = $this->actingAs($this->admin)->get(route('stock.index'));
        $response->assertStatus(200);

        // Check for gallery view buttons & modal
        $response->assertSee('Galeri Kad', false);
        $response->assertSee('Jadual', false);
        $response->assertSee('Ada Gambar', false);
        $response->assertSee('previewModalOpen', false);
    }

    public function test_stock_search_api_returns_image_metadata()
    {
        $response = $this->actingAs($this->admin)->getJson(route('stock.search-api', ['q' => 'ASM-AT-A']));
        $response->assertStatus(200);

        $data = $response->json();
        $this->assertIsArray($data);
        if (count($data) > 0) {
            $first = $data[0];
            $this->assertArrayHasKey('image_url', $first);
            $this->assertArrayHasKey('has_image', $first);
        }
    }

    public function test_kewps3_register_and_detail_views_render_image()
    {
        $response = $this->actingAs($this->admin)->get(route('stock-register.index'));
        $response->assertStatus(200);

        if ($this->itemWithImage) {
            $showResponse = $this->actingAs($this->admin)->get(route('stock-register.show', $this->itemWithImage));
            $showResponse->assertStatus(200);
            $showResponse->assertSee($this->itemWithImage->display_image, false);
            $showResponse->assertSee('Gambar Katalog', false);
        }
    }

    public function test_kad_petak_kewps4_renders_image()
    {
        if ($this->itemWithImage) {
            $response = $this->actingAs($this->admin)->get(route('stock.kad-petak', $this->itemWithImage));
            $response->assertStatus(200);
            $response->assertSee($this->itemWithImage->display_image, false);
        }
    }

    public function test_artisan_link_stock_images_command()
    {
        $exitCode = Artisan::call('stock:link-images');
        $this->assertEquals(0, $exitCode);
    }
}
