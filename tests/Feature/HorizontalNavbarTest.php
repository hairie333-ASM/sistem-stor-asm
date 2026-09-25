<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class HorizontalNavbarTest extends TestCase
{
    protected $pemohon;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pemohon = User::whereHas('role', fn($q) => $q->where('name', 'pemohon'))->first();
        $this->admin = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first();
    }

    public function test_horizontal_navbar_renders_for_pemohon()
    {
        $response = $this->actingAs($this->pemohon)->get(route('dashboard'));
        $response->assertStatus(200);

        // Masthead & Branding
        $response->assertSee('asm-logo-horizontal.png', false);
        $response->assertSee('SISTEM PENGURUSAN STOR KERAJAAN', false);
        $response->assertSee('UNIT PENGURUSAN STOR (TPS AM 6.1 - AM 6.10)', false);

        // Role Indicator & Demo Switcher
        $response->assertSee('Peranan Semasa:', false);
        $response->assertSee('Tukar Peranan Demo', false);

        // Horizontal Dropdowns
        $response->assertSee('Katalog Stok', false);
        $response->assertSee('Permohonan Saya', false);
        $response->assertSee('activeDropdown', false);

        // Action CTA & Profile
        $response->assertSee('Mohon Stok', false);
        $response->assertSee($this->pemohon->name, false);
    }

    public function test_horizontal_navbar_renders_for_admin()
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard'));
        $response->assertStatus(200);

        // Admin Menus & Dropdowns
        $response->assertSee('Stok & Katalog', false);
        $response->assertSee('Operasi & Agihan', false);
        $response->assertSee('Kawalan & Pematuhan', false);
        $response->assertSee('Laporan KEW.PS', false);
        $response->assertSee('Pentadbiran', false);
        $response->assertSee('Pengurusan Pengguna', false);
    }
}
