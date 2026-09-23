<?php

namespace Database\Seeders;

use App\Models\Disposal;
use App\Models\DisposalCommittee;
use App\Models\DisposalItem;
use App\Models\Location;
use App\Models\LossCase;
use App\Models\LossItem;
use App\Models\Receiving;
use App\Models\ReceivingItem;
use App\Models\Role;
use App\Models\SafetyInspection;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\StockBatch;
use App\Models\StockCategory;
use App\Models\StockItem;
use App\Models\StockRequest;
use App\Models\StockRequestItem;
use App\Models\StockTransaction;
use App\Models\StockTransactionItem;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\StockUnit;
use App\Models\StockVerification;
use App\Models\Store;
use App\Models\StoreSection;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\VerificationItem;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles & Permissions (9 Standard TPS Roles)
        $rolesData = [
            ['name' => 'admin', 'display_name' => 'System Administrator', 'description' => 'Akses penuh kepada konfigurasi sistem, pentadbiran pengguna, dan audit log.'],
            ['name' => 'ketua_jabatan', 'display_name' => 'Ketua Jabatan', 'description' => 'Kuasa melulus pelarasan, pelupusan, hapus kira dan laporan kedudukan stok.'],
            ['name' => 'pegawai_stor', 'display_name' => 'Pegawai Stor', 'description' => 'Pengurusan daftar stok KEW.PS-3, pengeluaran, pembungkusan dan lokasi simpanan.'],
            ['name' => 'pegawai_penerima', 'display_name' => 'Pegawai Penerima', 'description' => 'Memeriksa barang fizikal dan mengeluarkan KEW.PS-1 (BTB) atau KEW.PS-2 (BPB).'],
            ['name' => 'pegawai_pelulus', 'display_name' => 'Pegawai Pelulus', 'description' => 'Meluluskan permohonan pesanan stok individu (KEW.PS-8) dan antara stor (KEW.PS-7).'],
            ['name' => 'pemverifikasi', 'display_name' => 'Pemverifikasi Stor', 'description' => 'Lembaga bebas menjalankan pemeriksaan fizikal tahunan dan mengira percanggahan.'],
            ['name' => 'urus_setia_pelupusan', 'display_name' => 'Urus Setia Pelupusan', 'description' => 'Menguruskan lembaga pemeriksa pelupusan stok dan dokumentasi pelupusan.'],
            ['name' => 'urus_setia_kehilangan', 'display_name' => 'Urus Setia Kehilangan & Hapus Kira', 'description' => 'Mengendalikan siasatan kehilangan stok, laporan polis dan syor surcaj.'],
            ['name' => 'pemohon', 'display_name' => 'Pemohon / Staff', 'description' => 'Membuat pesanan stok, memantau status pesanan dan mengesahkan penerimaan stok.'],
        ];

        $roles = [];
        foreach ($rolesData as $r) {
            $roles[$r['name']] = Role::create($r);
        }

        // 2. Pre-seeded Users with secure standard password ('password123')
        $password = Hash::make('password123');

        $usersData = [
            ['name' => 'Ahmad Razak (Admin)', 'email' => 'admin@asm.gov.my', 'staff_id' => 'ASM-ADM-001', 'role_id' => $roles['admin']->id, 'department' => 'Unit Teknologi Maklumat', 'position' => 'Pegawai Teknologi Maklumat F44'],
            ['name' => 'Dr. Hazlina Binti Mansor', 'email' => 'ketua@asm.gov.my', 'staff_id' => 'ASM-MGT-001', 'role_id' => $roles['ketua_jabatan']->id, 'department' => 'Pejabat Ketua Pegawai Eksekutif', 'position' => 'Ketua Jabatan / Pengarah Operasi'],
            ['name' => 'Hairie Asmuni (Pegawai Stor)', 'email' => 'stor@asm.gov.my', 'staff_id' => 'ASM-STR-001', 'role_id' => $roles['pegawai_stor']->id, 'department' => 'Bahagian Pentadbiran & Stor', 'position' => 'Penolong Pegawai Tadbir Stor N29'],
            ['name' => 'Puan Siti Aminah', 'email' => 'penerima@asm.gov.my', 'staff_id' => 'ASM-STR-002', 'role_id' => $roles['pegawai_penerima']->id, 'department' => 'Bahagian Perolehan & Aset', 'position' => 'Pegawai Penerima Bertauliah N32'],
            ['name' => 'Encik Kamal Ariffin', 'email' => 'pelulus@asm.gov.my', 'staff_id' => 'ASM-MGT-002', 'role_id' => $roles['pegawai_pelulus']->id, 'department' => 'Bahagian Pengurusan & Sumber Manusia', 'position' => 'Pegawai Tadbir & Diplomatik M41'],
            ['name' => 'Dr. Tan Siew Ling (Pemverifikasi 1)', 'email' => 'verifikasi1@asm.gov.my', 'staff_id' => 'ASM-SCI-001', 'role_id' => $roles['pemverifikasi']->id, 'department' => 'Kluster Sains Hayat', 'position' => 'Pegawai Penyelidik Q48'],
            ['name' => 'Encik Ramesh Krishnan (Pemverifikasi 2)', 'email' => 'verifikasi2@asm.gov.my', 'staff_id' => 'ASM-FIN-001', 'role_id' => $roles['pemverifikasi']->id, 'department' => 'Bahagian Kewangan & Akaun', 'position' => 'Akauntan WA41'],
            ['name' => 'Puan Zuraida Ishak (Urus Setia Pelupusan)', 'email' => 'pelupusan@asm.gov.my', 'staff_id' => 'ASM-STR-003', 'role_id' => $roles['urus_setia_pelupusan']->id, 'department' => 'Unit Pengurusan Aset & Stor', 'position' => 'Penolong Pegawai Tadbir N29'],
            ['name' => 'Encik Farhan Bukhari (Urus Setia Kehilangan)', 'email' => 'kehilangan@asm.gov.my', 'staff_id' => 'ASM-LEG-001', 'role_id' => $roles['urus_setia_kehilangan']->id, 'department' => 'Unit Integriti & Perundangan', 'position' => 'Pegawai Integriti N41'],
            ['name' => 'Cik Aisyah Binti Zulkifli (Pemohon)', 'email' => 'pemohon@asm.gov.my', 'staff_id' => 'ASM-STF-001', 'role_id' => $roles['pemohon']->id, 'department' => 'Seksyen Program & Outreach', 'position' => 'Penolong Pegawai Sains C29'],
        ];

        $users = [];
        foreach ($usersData as $u) {
            $users[$u['email']] = User::create(array_merge($u, [
                'password' => $password,
                'phone' => '03-62030633',
                'is_active' => true,
            ]));
        }

        // 3. Stores (AM 6.1)
        $storeUtama = Store::create([
            'code' => 'STR-UTAMA',
            'name' => 'Stor Utama ASM (Pusat Pentadbiran)',
            'store_type' => 'UTAMA',
            'address' => 'Tingkat 20, Menara MATRADE, Jalan Sultan Haji Ahmad Shah, 50480 Kuala Lumpur',
            'officer_in_charge_id' => $users['stor@asm.gov.my']->id,
            'is_active' => true,
        ]);

        $storeAlatTulis = Store::create([
            'code' => 'STR-STATIONERY',
            'name' => 'Stor Unit Alat Tulis & Bekalan Pejabat',
            'store_type' => 'UNIT',
            'address' => 'Tingkat 19, Blok Pentadbiran ASM, Kuala Lumpur',
            'officer_in_charge_id' => $users['stor@asm.gov.my']->id,
            'is_active' => true,
        ]);

        $storeMakmal = Store::create([
            'code' => 'STR-LAB',
            'name' => 'Stor Unit Makmal & Peralatan Sains',
            'store_type' => 'UNIT',
            'address' => 'Kompleks Penyelidikan Sains ASM, Cyberjaya',
            'officer_in_charge_id' => $users['stor@asm.gov.my']->id,
            'is_active' => true,
        ]);

        // 4. Store Sections & Locations (AM 6.4)
        $secA = StoreSection::create(['store_id' => $storeUtama->id, 'code' => 'SEC-A', 'name' => 'Gudang Utama A (Kertas & Alat Tulis)']);
        $secB = StoreSection::create(['store_id' => $storeUtama->id, 'code' => 'SEC-B', 'name' => 'Seksyen B (Bekalan ICT & Elektronik)']);
        $secC = StoreSection::create(['store_id' => $storeMakmal->id, 'code' => 'SEC-LAB', 'name' => 'Seksyen Reagen & Bahan Kimia Makmal']);

        $locations = [];
        $locConfigs = [
            ['store' => $storeUtama, 'sec' => $secA, 'r' => '01', 'rk' => 'RA01', 'l' => '01', 'b' => '01'],
            ['store' => $storeUtama, 'sec' => $secA, 'r' => '01', 'rk' => 'RA01', 'l' => '02', 'b' => '01'],
            ['store' => $storeUtama, 'sec' => $secA, 'r' => '02', 'rk' => 'RA02', 'l' => '01', 'b' => '01'],
            ['store' => $storeUtama, 'sec' => $secB, 'r' => '01', 'rk' => 'RB01', 'l' => '01', 'b' => '01'],
            ['store' => $storeUtama, 'sec' => $secB, 'r' => '01', 'rk' => 'RB01', 'l' => '02', 'b' => '02'],
            ['store' => $storeMakmal, 'sec' => $secC, 'r' => '01', 'rk' => 'RC01', 'l' => '01', 'b' => '01'],
        ];

        foreach ($locConfigs as $cfg) {
            $code = sprintf("%s-%s-%s-%s-%s-%s", $cfg['store']->code, $cfg['sec']->code, $cfg['r'], $cfg['rk'], $cfg['l'], $cfg['b']);
            $locations[$code] = Location::create([
                'store_id' => $cfg['store']->id,
                'section_id' => $cfg['sec']->id,
                'row' => $cfg['r'],
                'rack' => $cfg['rk'],
                'level' => $cfg['l'],
                'bin' => $cfg['b'],
                'full_code' => $code,
                'barcode' => $code,
                'capacity' => 500,
                'is_active' => true,
            ]);
        }

        // 5. Stock Categories & Units of Measurement
        $catStationery = StockCategory::create(['code' => 'CAT-01', 'name' => 'Alat Tulis & Kertas', 'description' => 'Bekalan am pejabat dan percetakan']);
        $catICT = StockCategory::create(['code' => 'CAT-02', 'name' => 'Aksesori ICT & Elektronik', 'description' => 'Peralatan sokongan komputer dan gajet']);
        $catLab = StockCategory::create(['code' => 'CAT-03', 'name' => 'Bahan Kimia & Reagen Makmal', 'description' => 'Bekalan penyelidikan bertarikh luput']);
        $catSafety = StockCategory::create(['code' => 'CAT-04', 'name' => 'Peralatan Keselamatan & PPE', 'description' => 'Pelindung diri dan perubatan pertolongan cemas']);

        $uomRim = StockUnit::create(['code' => 'RIM', 'name' => 'Rim', 'symbol' => 'rim']);
        $uomBox = StockUnit::create(['code' => 'KOTAK', 'name' => 'Kotak', 'symbol' => 'ktk']);
        $uomUnit = StockUnit::create(['code' => 'UNIT', 'name' => 'Unit', 'symbol' => 'unit']);
        $uomBottle = StockUnit::create(['code' => 'BOTOL', 'name' => 'Botol', 'symbol' => 'btl']);
        $uomPacket = StockUnit::create(['code' => 'PAKET', 'name' => 'Paket', 'symbol' => 'pkt']);

        // 6. Stock Items (Catalogue & Master)
        $defaultLoc = Location::first();

        $stockData = [
            [
                'kad_no' => 'KAD-2026-001',
                'stock_code' => 'ASM-AT-001',
                'description' => 'Kertas A4 80gsm Putih (IK Copy / Double A)',
                'category_id' => $catStationery->id,
                'stock_group' => 'A',
                'movement' => 'CEPAT',
                'uom_id' => $uomRim->id,
                'min_level' => 50,
                'reorder_level' => 100,
                'max_level' => 150,
                'unit_price' => 14.50,
                'supplier_name' => 'Sinar Cahaya Enterprise',
                'current_quantity' => 120,
            ],
            [
                'kad_no' => 'KAD-2026-002',
                'stock_code' => 'ASM-AT-002',
                'description' => 'Pen Mata Bulat Pilot G-2 0.7mm (Hitam)',
                'category_id' => $catStationery->id,
                'stock_group' => 'B',
                'movement' => 'CEPAT',
                'uom_id' => $uomBox->id,
                'min_level' => 20,
                'reorder_level' => 40,
                'max_level' => 60,
                'unit_price' => 42.00,
                'supplier_name' => 'Sinar Cahaya Enterprise',
                'current_quantity' => 35, // Below reorder
            ],
            [
                'kad_no' => 'KAD-2026-003',
                'stock_code' => 'ASM-ICT-001',
                'description' => 'Toner Pencetak HP LaserJet Enterprise M608 (Black)',
                'category_id' => $catICT->id,
                'stock_group' => 'A',
                'movement' => 'CEPAT',
                'uom_id' => $uomUnit->id,
                'min_level' => 10,
                'reorder_level' => 20,
                'max_level' => 30,
                'unit_price' => 650.00,
                'supplier_name' => 'Infotech Solutions Sdn Bhd',
                'current_quantity' => 18,
            ],
            [
                'kad_no' => 'KAD-2026-004',
                'stock_code' => 'ASM-ICT-002',
                'description' => 'Pemacu Kilat USB 64GB Kingston DataTraveler',
                'category_id' => $catICT->id,
                'stock_group' => 'B',
                'movement' => 'PERLAHAN',
                'uom_id' => $uomUnit->id,
                'min_level' => 15,
                'reorder_level' => 30,
                'max_level' => 50,
                'unit_price' => 28.00,
                'supplier_name' => 'Infotech Solutions Sdn Bhd',
                'current_quantity' => 8, // Below min alert!
            ],
            [
                'kad_no' => 'KAD-2026-005',
                'stock_code' => 'ASM-LAB-001',
                'description' => 'Ethanol 99.8% Gred Analisis Makmal 2.5L',
                'category_id' => $catLab->id,
                'stock_group' => 'A',
                'movement' => 'CEPAT',
                'uom_id' => $uomBottle->id,
                'min_level' => 8,
                'reorder_level' => 15,
                'max_level' => 25,
                'unit_price' => 185.00,
                'supplier_name' => 'Mega Science Reagents Bhd',
                'is_expiry_controlled' => true,
                'is_batch_controlled' => true,
                'current_quantity' => 16,
            ],
            [
                'kad_no' => 'KAD-2026-006',
                'stock_code' => 'ASM-LAB-002',
                'description' => 'Kit Ujian Enzim ELISA Pengesanan Protein',
                'category_id' => $catLab->id,
                'stock_group' => 'A',
                'movement' => 'PERLAHAN',
                'uom_id' => $uomBox->id,
                'min_level' => 5,
                'reorder_level' => 10,
                'max_level' => 15,
                'unit_price' => 1200.00,
                'supplier_name' => 'Mega Science Reagents Bhd',
                'is_expiry_controlled' => true,
                'is_batch_controlled' => true,
                'current_quantity' => 6,
            ],
            [
                'kad_no' => 'KAD-2026-007',
                'stock_code' => 'ASM-PPE-001',
                'description' => 'Pelitup Muka Pembedahan 3-Lapis (50 keping/kotak)',
                'category_id' => $catSafety->id,
                'stock_group' => 'B',
                'movement' => 'CEPAT',
                'uom_id' => $uomBox->id,
                'min_level' => 30,
                'reorder_level' => 60,
                'max_level' => 100,
                'unit_price' => 12.00,
                'supplier_name' => 'Medik Jaya Global',
                'current_quantity' => 75,
            ],
            [
                'kad_no' => 'KAD-2026-008',
                'stock_code' => 'ASM-PPE-002',
                'description' => 'Sarung Tangan Nitrile Bebas Serbuk (Saiz M)',
                'category_id' => $catSafety->id,
                'stock_group' => 'B',
                'movement' => 'CEPAT',
                'uom_id' => $uomBox->id,
                'min_level' => 25,
                'reorder_level' => 50,
                'max_level' => 80,
                'unit_price' => 26.50,
                'supplier_name' => 'Medik Jaya Global',
                'current_quantity' => 90, // Above max alert!
            ],
        ];

        $stockItems = [];
        foreach ($stockData as $s) {
            $stockItems[$s['stock_code']] = StockItem::create(array_merge($s, [
                'default_location_id' => $defaultLoc->id,
                'status' => 'AVAILABLE',
                'reserved_quantity' => 0,
                'quarantine_quantity' => 0,
                'damaged_quantity' => 0,
                'disposal_quantity' => 0,
                'written_off_quantity' => 0,
            ]));
        }

        // 7. Seed Batches for Expiry Control (AM 6.3 / KEW.PS-6)
        $now = Carbon::now();

        // Expired batch
        StockBatch::create([
            'stock_item_id' => $stockItems['ASM-LAB-001']->id,
            'batch_number' => 'BCH-2025-ETH-01',
            'expiry_date' => $now->copy()->subDays(15),
            'quantity' => 4,
            'remaining_quantity' => 4,
            'unit_price' => 185.00,
            'status' => 'EXPIRED',
        ]);

        // Near expiry batch (< 30 days)
        StockBatch::create([
            'stock_item_id' => $stockItems['ASM-LAB-002']->id,
            'batch_number' => 'BCH-2026-ELISA-99',
            'expiry_date' => $now->copy()->addDays(20),
            'quantity' => 2,
            'remaining_quantity' => 2,
            'unit_price' => 1200.00,
            'status' => 'AVAILABLE',
        ]);

        // Normal future expiry batch (> 90 days)
        StockBatch::create([
            'stock_item_id' => $stockItems['ASM-LAB-001']->id,
            'batch_number' => 'BCH-2026-ETH-02',
            'expiry_date' => $now->copy()->addMonths(8),
            'quantity' => 12,
            'remaining_quantity' => 12,
            'unit_price' => 185.00,
            'status' => 'AVAILABLE',
        ]);

        // 8. Seed Historical Transactions for KEW.PS-3 Bahagian B
        $txn1 = StockTransaction::create([
            'transaction_number' => 'TXN/ASM/' . date('Y') . '/00001',
            'transaction_type' => 'RECEIPT',
            'kew_ps_type' => 'KEW.PS-1',
            'reference_number' => 'BTB/ASM/' . date('Y') . '/0001',
            'store_id' => $storeUtama->id,
            'user_id' => $users['penerima@asm.gov.my']->id,
            'party_name' => 'Sinar Cahaya Enterprise',
            'transaction_date' => $now->copy()->subDays(20)->toDateString(),
            'notes' => 'Penerimaan Stok Kertas dan Alat Tulis Pesanan Rasmi PO-2026-091',
        ]);

        StockTransactionItem::create([
            'transaction_id' => $txn1->id,
            'stock_item_id' => $stockItems['ASM-AT-001']->id,
            'movement_type' => 'IN',
            'quantity' => 150,
            'unit_price' => 14.50,
            'total_price' => 2175.00,
            'balance_quantity_before' => 0,
            'balance_quantity_after' => 150,
            'balance_value_after' => 2175.00,
            'remarks' => 'Penerimaan Pembekal Sinar Cahaya',
        ]);

        $txn2 = StockTransaction::create([
            'transaction_number' => 'TXN/ASM/' . date('Y') . '/00002',
            'transaction_type' => 'ISSUE',
            'kew_ps_type' => 'KEW.PS-8',
            'reference_number' => 'PS8/ASM/' . date('Y') . '/0001',
            'store_id' => $storeUtama->id,
            'user_id' => $users['stor@asm.gov.my']->id,
            'party_name' => 'Cik Aisyah (Seksyen Program & Outreach)',
            'transaction_date' => $now->copy()->subDays(10)->toDateString(),
            'notes' => 'Pengeluaran Stok Kertas A4 untuk Mesyuarat Agung Tahunan',
        ]);

        StockTransactionItem::create([
            'transaction_id' => $txn2->id,
            'stock_item_id' => $stockItems['ASM-AT-001']->id,
            'movement_type' => 'OUT',
            'quantity' => 30,
            'unit_price' => 14.50,
            'total_price' => 435.00,
            'balance_quantity_before' => 150,
            'balance_quantity_after' => 120,
            'balance_value_after' => 1740.00,
            'remarks' => 'Pengeluaran diluluskan oleh En. Kamal',
        ]);

        // 9. Seed Receiving Record (KEW.PS-1 BTB)
        $rec1 = Receiving::create([
            'btb_number' => 'BTB/ASM/' . date('Y') . '/0001',
            'store_id' => $storeUtama->id,
            'supplier_name' => 'Sinar Cahaya Enterprise',
            'supplier_address' => 'No. 14, Jalan Industri Cheras, 56000 Kuala Lumpur',
            'receipt_type' => 'PURCHASE',
            'po_contract_number' => 'ASM/PO/2026/091',
            'po_contract_date' => $now->copy()->subDays(25),
            'delivery_order_number' => 'DO-SCE-8831',
            'delivery_order_date' => $now->copy()->subDays(20),
            'carrier_info' => 'Lori Syarikat Pembekal (WXT 8821) - Pemandu: En. Roslan',
            'status' => 'ACCEPTED',
            'receiving_officer_id' => $users['penerima@asm.gov.my']->id,
            'inspection_date' => $now->copy()->subDays(20),
            'remarks' => 'Barang diterima dalam bungkusan yang sempurna dan kuantiti tepat.',
        ]);

        ReceivingItem::create([
            'receiving_id' => $rec1->id,
            'stock_item_id' => $stockItems['ASM-AT-001']->id,
            'ordered_quantity' => 150,
            'do_quantity' => 150,
            'received_quantity' => 150,
            'accepted_quantity' => 150,
            'rejected_quantity' => 0,
            'unit_price' => 14.50,
            'total_price' => 2175.00,
            'status' => 'ACCEPTED',
        ]);

        // 10. Seed Staff Request (KEW.PS-8)
        $req1 = StockRequest::create([
            'request_number' => 'PS8/ASM/' . date('Y') . '/0001',
            'form_type' => 'KEW.PS-8',
            'store_id' => $storeUtama->id,
            'requester_id' => $users['pemohon@asm.gov.my']->id,
            'department' => 'Seksyen Program & Outreach',
            'purpose' => 'Persediaan Bengkel Sains Komuniti Kebangsaan ASM 2026',
            'priority' => 'NORMAL',
            'status' => 'COMPLETED',
            'approver_id' => $users['pelulus@asm.gov.my']->id,
            'approved_at' => $now->copy()->subDays(11),
            'approval_remarks' => 'Diluluskan mengikut keperluan aktiviti program.',
            'issuer_id' => $users['stor@asm.gov.my']->id,
            'issued_at' => $now->copy()->subDays(10),
            'recipient_id' => $users['pemohon@asm.gov.my']->id,
            'received_at' => $now->copy()->subDays(10),
            'recipient_notes' => 'Telah disemak dan diterima dalam keadaan lengkap.',
        ]);

        StockRequestItem::create([
            'stock_request_id' => $req1->id,
            'stock_item_id' => $stockItems['ASM-AT-001']->id,
            'requested_quantity' => 30,
            'approved_quantity' => 30,
            'issued_quantity' => 30,
        ]);

        // 11. Seed Pending Request for Live Testing
        $req2 = StockRequest::create([
            'request_number' => 'PS8/ASM/' . date('Y') . '/0002',
            'form_type' => 'KEW.PS-8',
            'store_id' => $storeUtama->id,
            'requester_id' => $users['pemohon@asm.gov.my']->id,
            'department' => 'Seksyen Program & Outreach',
            'purpose' => 'Penggantian toner pencetak pejabat urus setia',
            'priority' => 'URGENT',
            'status' => 'SUBMITTED', // Pending approval by Pegawai Pelulus
        ]);

        StockRequestItem::create([
            'stock_request_id' => $req2->id,
            'stock_item_id' => $stockItems['ASM-ICT-001']->id,
            'requested_quantity' => 2,
            'approved_quantity' => 0,
            'issued_quantity' => 0,
        ]);

        // 12. Seed Verification Board (AM 6.6 / KEW.PS-11, 12, 13)
        $veri = StockVerification::create([
            'verification_number' => 'PS11/ASM/' . date('Y') . '/0001',
            'store_id' => $storeUtama->id,
            'year' => date('Y'),
            'appointment_letter_ref' => 'ASM/STR/VERI/2026/01',
            'scheduled_date' => $now->copy()->subDays(5),
            'start_date' => $now->copy()->subDays(5),
            'end_date' => $now->copy()->subDays(4),
            'verifier_1_id' => $users['verifikasi1@asm.gov.my']->id,
            'verifier_2_id' => $users['verifikasi2@asm.gov.my']->id,
            'status' => 'APPROVED',
            'report_number' => 'KEW.PS-12/ASM/' . date('Y') . '/001',
            'cert_number' => 'KEW.PS-13/ASM/' . date('Y') . '/001',
            'approval_officer_id' => $users['ketua@asm.gov.my']->id,
            'approved_at' => $now->copy()->subDays(3),
            'findings_summary' => 'Pemeriksaan fizikal mendapati rekod stok adalah teratur dan mematuhi Tatacara Pengurusan Stor AM 6.6.',
            'corrective_actions' => 'Tiada tindakan pembetulan kritikal diperlukan.',
        ]);

        foreach ($stockItems as $item) {
            VerificationItem::create([
                'verification_id' => $veri->id,
                'stock_item_id' => $item->id,
                'system_quantity' => $item->current_quantity,
                'physical_quantity' => $item->current_quantity,
                'variance_quantity' => 0,
                'unit_price' => $item->unit_price,
                'condition_status' => 'BAIK',
            ]);
        }

        // 13. Seed Safety & Cleanliness Inspection (AM 6.7)
        SafetyInspection::create([
            'store_id' => $storeUtama->id,
            'inspection_date' => $now->copy()->subDays(7),
            'inspector_id' => $users['stor@asm.gov.my']->id,
            'category' => 'FIRE_SAFETY',
            'score' => 95,
            'status' => 'PASS',
            'remarks' => 'Semua alat pemadam api mempunyai sijil BOMBA yang sah dan laluan kecemasan tidak terhalang.',
            'checklist_items' => [
                'Alat pemadam api dalam keadaan baik' => true,
                'Hos bomba berfungsi' => true,
                'Pintu rintangan api bebas halangan' => true,
                'Lampu kecemasan berfungsi' => true,
            ],
        ]);

        // 14. Seed Default System Settings
        $defaultSettings = [
            'org_name' => 'Akademi Sains Malaysia (ASM)',
            'org_code' => 'ASM',
            'org_address' => 'Tingkat 20, Menara MATRADE, Jalan Sultan Haji Ahmad Shah, 50480 Kuala Lumpur',
            'phone' => '+603-6203 0633',
            'email' => 'stor@akademisains.gov.my',
            'default_currency' => 'RM',
            'fiscal_year' => date('Y'),
            'turnover_target' => '4.0',
            'prefix_btb' => 'BTB/ASM',
            'prefix_bpb' => 'BPB/ASM',
            'prefix_ps7' => 'PS7/ASM',
            'prefix_ps8' => 'PS8/ASM',
            'prefix_ps9' => 'PS9/ASM',
            'prefix_ps17' => 'PS17/ASM',
            'prefix_ps20' => 'PS20/ASM',
            'prefix_ps32' => 'PS32/ASM',
        ];

        foreach ($defaultSettings as $k => $v) {
            SystemSetting::set($k, $v);
        }

        // 15. Automatically seed 981 real stock items from September 2026 data if available
        if (file_exists(storage_path('app/stok_sept_2026/extracted_stock_data.json'))) {
            \Illuminate\Support\Facades\Artisan::call('stock:import-sept-2026');
        }
    }
}
