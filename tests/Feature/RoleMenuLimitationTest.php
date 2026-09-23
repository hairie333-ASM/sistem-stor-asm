<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleMenuLimitationTest extends TestCase
{
    /**
     * Test that Pemohon sees only catalog and request related menus.
     */
    public function test_pemohon_sees_only_catalog_and_request_menus(): void
    {
        $pemohon = User::whereHas('role', fn($q) => $q->where('name', 'pemohon'))->first();
        if (!$pemohon) {
            $pemohon = User::factory()->create();
        }

        $response = $this->actingAs($pemohon)->get(route('dashboard'));

        $response->assertStatus(200);

        // Permitted items for Pemohon
        $response->assertSee('Papan Pemuka Pemohon');
        $response->assertSee('Borang Permohonan Stok');
        $response->assertSee('Senarai Permohonan Saya');
        $response->assertSee('Katalog Stok ASM');
        $response->assertSee('Pemulangan Stok');

        // Restricted items that Pemohon MUST NOT see in menu
        $response->assertDontSee('Daftar Stok (KEW.PS-3)');
        $response->assertDontSee('Lokasi Penyimpanan');
        $response->assertDontSee('Tarikh Luput (KEW.PS-6)');
        $response->assertDontSee('Kumpulan A & B (KEW.PS-5)', false);
        $response->assertDontSee('Penerimaan (KEW.PS-1/2)');
        $response->assertDontSee('Pindahan Stor (KEW.PS-17)');
        $response->assertDontSee('Verifikasi & Pelarasan', false);
        $response->assertDontSee('Pelupusan (KEW.PS-19..31)');
        $response->assertDontSee('Kehilangan & Hapus Kira', false);
        $response->assertDontSee('Hub Borang KEW.PS-1..36');
        $response->assertDontSee('Pengurusan Pengguna');
    }

    /**
     * Test that Pegawai Pelulus sees only approval and catalog menus.
     */
    public function test_pegawai_pelulus_sees_approval_and_catalog_menus(): void
    {
        $pelulus = User::whereHas('role', fn($q) => $q->where('name', 'pegawai_pelulus'))->first();
        if (!$pelulus) {
            $this->markTestSkipped('Pegawai Pelulus not found');
        }

        $response = $this->actingAs($pelulus)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Papan Pemuka Pelulus');
        $response->assertSee('Kelulusan Pesanan (KEW.PS-7/8)');
        $response->assertSee('Semakan Katalog Stok');

        // Restricted items
        $response->assertDontSee('Daftar Stok (KEW.PS-3)');
        $response->assertDontSee('Penerimaan (KEW.PS-1/2)');
        $response->assertDontSee('Pelupusan (KEW.PS-19..31)');
        $response->assertDontSee('Pengurusan Pengguna');
    }

    /**
     * Test that Pegawai Penerima sees receiving and catalog menus.
     */
    public function test_pegawai_penerima_sees_receiving_and_catalog_menus(): void
    {
        $penerima = User::whereHas('role', fn($q) => $q->where('name', 'pegawai_penerima'))->first();
        if (!$penerima) {
            $this->markTestSkipped('Pegawai Penerima not found');
        }

        $response = $this->actingAs($penerima)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Papan Pemuka Penerimaan');
        $response->assertSee('Senarai Penerimaan (BTB)');
        $response->assertSee('Terima Barang Baru (KEW.PS-1)');
        $response->assertSee('Katalog Stok ASM');

        // Restricted items
        $response->assertDontSee('Daftar Stok (KEW.PS-3)');
        $response->assertDontSee('Pelupusan (KEW.PS-19..31)');
        $response->assertDontSee('Pengurusan Pengguna');
    }

    /**
     * Test that Pegawai Stor and Admin see full operations menus.
     */
    public function test_pegawai_stor_and_admin_see_full_menus(): void
    {
        $pegawaiStor = User::whereHas('role', fn($q) => $q->where('name', 'pegawai_stor'))->first();
        if ($pegawaiStor) {
            $response = $this->actingAs($pegawaiStor)->get(route('dashboard'));
            $response->assertStatus(200);
            $response->assertSee('Katalog Stok Induk');
            $response->assertSee('Daftar Stok (KEW.PS-3)');
            $response->assertSee('Penerimaan (KEW.PS-1/2)');
            $response->assertSee('Pesanan Stok (KEW.PS-7/8)');
            $response->assertSee('Verifikasi & Pelarasan', false);
            $response->assertSee('Pelupusan (KEW.PS-19..31)');
        }

        $admin = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first();
        if ($admin) {
            $response = $this->actingAs($admin)->get(route('dashboard'));
            $response->assertStatus(200);
            $response->assertSee('Pengurusan Pengguna');
            $response->assertSee('Tetapan Stor & Sistem', false);
            $response->assertSee('Jejak Audit (Audit Trail)');
        }
    }

    /**
     * Test Pemverifikasi, Pelupusan, Kehilangan and Ketua Jabatan menus.
     */
    public function test_specialized_roles_see_their_respective_menus(): void
    {
        // 1. Pemverifikasi
        $verif = User::whereHas('role', fn($q) => $q->where('name', 'pemverifikasi'))->first();
        if ($verif) {
            $res = $this->actingAs($verif)->get(route('dashboard'));
            $res->assertStatus(200);
            $res->assertSee('Papan Pemuka Verifikasi');
            $res->assertSee('Laporan Verifikasi (KEW.PS-10..13)');
            $res->assertDontSee('Pengurusan Pengguna');
        }

        // 2. Urus Setia Pelupusan
        $pelupusan = User::whereHas('role', fn($q) => $q->where('name', 'urus_setia_pelupusan'))->first();
        if ($pelupusan) {
            $res = $this->actingAs($pelupusan)->get(route('dashboard'));
            $res->assertStatus(200);
            $res->assertSee('Papan Pemuka Pelupusan');
            $res->assertSee('Senarai Pelupusan (KEW.PS-19..31)');
            $res->assertDontSee('Pengurusan Pengguna');
        }

        // 3. Urus Setia Kehilangan
        $kehilangan = User::whereHas('role', fn($q) => $q->where('name', 'urus_setia_kehilangan'))->first();
        if ($kehilangan) {
            $res = $this->actingAs($kehilangan)->get(route('dashboard'));
            $res->assertStatus(200);
            $res->assertSee('Papan Pemuka Kehilangan');
            $res->assertSee('Senarai Kes (KEW.PS-32..36)');
            $res->assertDontSee('Pengurusan Pengguna');
        }

        // 4. Ketua Jabatan
        $ketua = User::whereHas('role', fn($q) => $q->where('name', 'ketua_jabatan'))->first();
        if ($ketua) {
            $res = $this->actingAs($ketua)->get(route('dashboard'));
            $res->assertStatus(200);
            $res->assertSee('Dashboard Eksekutif');
            $res->assertSee('Tindakan Kelulusan');
            $res->assertSee('Kelulusan Pelupusan (KEW.PS-21)');
            $res->assertSee('Kelulusan Hapus Kira (KEW.PS-35)');
        }
    }
}
