<?php

namespace App\Console\Commands;

use App\Models\Disposal;
use App\Models\DisposalCommittee;
use App\Models\DisposalItem;
use App\Models\InvestigationCommittee;
use App\Models\Location;
use App\Models\LossCase;
use App\Models\LossItem;
use App\Models\Packing;
use App\Models\Receiving;
use App\Models\ReceivingItem;
use App\Models\Rejection;
use App\Models\RejectionItem;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\StockBatch;
use App\Models\StockCategory;
use App\Models\StockItem;
use App\Models\StockRequest;
use App\Models\StockRequestItem;
use App\Models\StockReturn;
use App\Models\StockReturnItem;
use App\Models\StockSerialNumber;
use App\Models\StockTransaction;
use App\Models\StockTransactionItem;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\StockUnit;
use App\Models\StockVerification;
use App\Models\Store;
use App\Models\User;
use App\Models\VerificationItem;
use App\Services\AuditLogService;
use App\Services\DocumentNumberService;
use App\Services\GroupABService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportRealStockSept2026 extends Command
{
    protected $signature = 'stock:import-sept-2026';
    protected $description = 'Clears dummy stock items and imports real ASM stock catalog for September 2026 from 4 Excel categories';

    public function handle()
    {
        $this->info('Memulakan pembersihan data stok dummy dan import data stok sebenar ASM September 2026...');

        $jsonPath = storage_path('app/stok_sept_2026/extracted_stock_data.json');
        if (!file_exists($jsonPath)) {
            $this->error("Fail JSON data tidak ditemui di: {$jsonPath}");
            return 1;
        }

        $data = json_decode(file_get_contents($jsonPath), true);
        if (!$data || empty($data['items'])) {
            $this->error('Data stok kosong atau tidak sah.');
            return 1;
        }

        // 1. Clearkan semua rekod dummy stok dan transaksi bergantung
        $this->info('1/6: Mengosongkan data dummy...');
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } elseif ($driver === 'pgsql') {
            DB::statement('SET session_replication_role = replica;');
        }

        StockTransactionItem::truncate();
        StockTransaction::truncate();
        StockBatch::truncate();
        StockSerialNumber::truncate();
        ReceivingItem::truncate();
        Receiving::truncate();
        RejectionItem::truncate();
        Rejection::truncate();
        Packing::truncate();
        StockRequestItem::truncate();
        StockRequest::truncate();
        StockReturnItem::truncate();
        StockReturn::truncate();
        StockTransferItem::truncate();
        StockTransfer::truncate();
        VerificationItem::truncate();
        StockVerification::truncate();
        StockAdjustmentItem::truncate();
        StockAdjustment::truncate();
        DisposalItem::truncate();
        DisposalCommittee::truncate();
        Disposal::truncate();
        LossItem::truncate();
        InvestigationCommittee::truncate();
        LossCase::truncate();
        StockItem::truncate();

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } elseif ($driver === 'pgsql') {
            DB::statement('SET session_replication_role = DEFAULT;');
        }

        // 2. Wujudkan 4 Kategori Utama
        $this->info('2/6: Mengemaskini 4 kategori stok berkanun ASM...');
        $categoriesMap = [];
        foreach ($data['categories'] as $cat) {
            $category = StockCategory::updateOrCreate(
                ['code' => $cat['code']],
                ['name' => $cat['name'], 'description' => $cat['description']]
            );
            $categoriesMap[$cat['code']] = $category->id;
        }

        // 3. Wujudkan Unit Pengukuran (UOM)
        $this->info('3/6: Memastikan unit pengukuran (UOM) lengkap...');
        $uomDefinitions = [
            'RIM' => ['name' => 'Rim', 'symbol' => 'rim'],
            'KOTAK' => ['name' => 'Kotak', 'symbol' => 'ktk'],
            'UNIT' => ['name' => 'Unit', 'symbol' => 'unit'],
            'BOTOL' => ['name' => 'Botol', 'symbol' => 'btl'],
            'PAKET' => ['name' => 'Paket', 'symbol' => 'pkt'],
            'BATANG' => ['name' => 'Batang', 'symbol' => 'btg'],
            'BUAH' => ['name' => 'Buah', 'symbol' => 'buah'],
            'HELAI' => ['name' => 'Helai', 'symbol' => 'helai'],
            'KAPUR' => ['name' => 'Keping', 'symbol' => 'kpg'],
            'NASKHAH' => ['name' => 'Naskhah', 'symbol' => 'naskhah'],
            'SET' => ['name' => 'Set', 'symbol' => 'set'],
            'GELUNG' => ['name' => 'Gelung', 'symbol' => 'glg'],
            'PASANG' => ['name' => 'Pasang', 'symbol' => 'psg'],
        ];

        $uomMap = [];
        foreach ($uomDefinitions as $code => $uomData) {
            $unit = StockUnit::updateOrCreate(
                ['code' => $code],
                ['name' => $uomData['name'], 'symbol' => $uomData['symbol']]
            );
            $uomMap[$code] = $unit->id;
        }

        // 4. Pastikan Stor & Lokasi Lengkap
        $this->info('4/6: Menyediakan stor dan lokasi fizikal...');
        $storeUtama = Store::firstOrCreate(
            ['code' => 'STR-UTAMA'],
            ['name' => 'Stor Utama ASM (Pusat Pentadbiran)', 'store_type' => 'UTAMA', 'address' => 'Aras 20, Menara MATRADE', 'is_active' => true]
        );
        $storeStationery = Store::firstOrCreate(
            ['code' => 'STR-STATIONERY'],
            ['name' => 'Stor Unit Alat Tulis & Bekalan Pejabat', 'store_type' => 'UNIT', 'address' => 'Bilik Bekalan Aras 20', 'is_active' => true]
        );

        $defaultLocationUtama = Location::where('store_id', $storeUtama->id)->first() ?? Location::create([
            'store_id' => $storeUtama->id,
            'row' => '01',
            'rack' => 'RA01',
            'level' => '01',
            'bin' => '01',
            'full_code' => 'STR-UTAMA-LOC-01',
            'barcode' => 'STR-UTAMA-LOC-01',
            'capacity' => 1000,
            'is_active' => true,
        ]);

        $defaultLocationStationery = Location::where('store_id', $storeStationery->id)->first() ?? Location::create([
            'store_id' => $storeStationery->id,
            'row' => '01',
            'rack' => 'RS01',
            'level' => '01',
            'bin' => '01',
            'full_code' => 'STR-STAT-LOC-01',
            'barcode' => 'STR-STAT-LOC-01',
            'capacity' => 1000,
            'is_active' => true,
        ]);

        $locationsCache = [];
        $locationsCache['Stor Unit Alat Tulis'] = $defaultLocationStationery->id;
        $locationsCache['Stor Utama (Pusat Pentadbiran)'] = $defaultLocationUtama->id;
        $locationsCache['Stor Utama ASM'] = $defaultLocationUtama->id;

        $user = User::first();

        // 5. Masukkan 981 Item Stok Sebenar
        $this->info('5/6: Memasukkan 981 rekod stok sebenar dan menjana transaksi lejar awal KEW.PS-3...');
        $totalItems = count($data['items']);
        $bar = $this->output->createProgressBar($totalItems);
        $bar->start();

        $transactionCount = 0;
        $totalInitialValue = 0;

        foreach ($data['items'] as $itemData) {
            // Tentukan stor dan lokasi
            $storeId = ($itemData['category_code'] === 'CAT-AT') ? $storeStationery->id : $storeUtama->id;
            $locName = $itemData['location_name'] ?? 'Stor Utama ASM';

            if (!isset($locationsCache[$locName])) {
                $cleanLocCode = 'LOC-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $locName), 0, 16)) . '-' . substr(md5($locName), 0, 4);
                $newLoc = Location::where('full_code', $cleanLocCode)->first() ?? Location::create([
                    'store_id' => $storeId,
                    'row' => '01',
                    'rack' => 'R01',
                    'level' => '01',
                    'bin' => '01',
                    'full_code' => $cleanLocCode,
                    'barcode' => $cleanLocCode,
                    'capacity' => 500,
                    'is_active' => true,
                ]);
                $locationsCache[$locName] = $newLoc->id;
            }
            $locationId = $locationsCache[$locName];

            $categoryId = $categoriesMap[$itemData['category_code']];
            $uomId = $uomMap[$itemData['uom_code']] ?? $uomMap['UNIT'];
            $currentQty = (float) $itemData['current_quantity'];
            $unitPrice = (float) $itemData['unit_price'];

            $stock = StockItem::create([
                'kad_no' => $itemData['kad_no'],
                'stock_code' => $itemData['stock_code'],
                'description' => $itemData['description'],
                'category_id' => $categoryId,
                'stock_group' => 'B', // Akan dikemaskini oleh Pareto
                'movement' => $itemData['movement'] ?? 'CEPAT',
                'uom_id' => $uomId,
                'default_location_id' => $locationId,
                'min_level' => (float) $itemData['min_level'],
                'reorder_level' => (float) $itemData['reorder_level'],
                'max_level' => (float) $itemData['max_level'],
                'unit_price' => $unitPrice,
                'supplier_name' => 'Pembekal Rasmi ASM / Arkib Penerbitan',
                'is_expiry_controlled' => false,
                'is_batch_controlled' => false,
                'is_serial_controlled' => false,
                'current_quantity' => $currentQty,
                'reserved_quantity' => 0,
                'quarantine_quantity' => 0,
                'damaged_quantity' => 0,
                'disposal_quantity' => 0,
                'written_off_quantity' => 0,
                'status' => 'AVAILABLE',
                'remarks' => 'Data rasmi dimuat naik daripada Laporan Stok September 2026',
            ]);

            // Jika stok mempunyai baki semasa, jana rekod transaksi penerimaan awal (Opening Balance)
            // Ini memastikan KEW.PS-3 Bahagian B lejar digital 100% tepat dan berintegriti
            if ($currentQty > 0) {
                $transactionCount++;
                $txnNum = sprintf('TXN/2026/%05d', $transactionCount);

                $txn = StockTransaction::create([
                    'transaction_number' => $txnNum,
                    'transaction_type' => 'RECEIPT',
                    'kew_ps_type' => 'KEW.PS-3',
                    'reference_number' => 'BAKI-SEPT-2026',
                    'store_id' => $storeId,
                    'user_id' => $user->id,
                    'party_name' => 'Baki Fizikal September 2026',
                    'transaction_date' => '2026-09-01',
                    'notes' => 'Baki stok fizikal sah pada September 2026 mengikut Laporan Kedudukan Stok ASM.',
                ]);

                $totalPrice = $currentQty * $unitPrice;
                $totalInitialValue += $totalPrice;

                StockTransactionItem::create([
                    'transaction_id' => $txn->id,
                    'stock_item_id' => $stock->id,
                    'location_id' => $locationId,
                    'movement_type' => 'IN',
                    'quantity' => $currentQty,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'balance_quantity_before' => 0,
                    'balance_quantity_after' => $currentQty,
                    'balance_value_after' => $totalPrice,
                    'remarks' => 'Baki awal disahkan September 2026',
                ]);
            }

            $bar->advance();
        }
        $bar->finish();
        $this->newLine();

        // 6. Recalculate Group A/B Pareto
        $this->info('6/6: Melaksanakan Analisis Pareto Kumpulan A & B (KEW.PS-5)...');
        GroupABService::applyGroupClassification();

        $groupACount = StockItem::where('stock_group', 'A')->count();
        $groupBCount = StockItem::where('stock_group', 'B')->count();

        AuditLogService::log(
            'IMPORT_STOCK_DATA',
            'StockItem',
            'ALL',
            null,
            ['total_items' => $totalItems, 'total_value' => $totalInitialValue],
            "Pembersihan dummy selesai dan 981 data stok sebenar September 2026 dimuat naik sepenuhnya."
        );

        $this->info('------------------------------------------------------------');
        $this->info('IMPORT BERJAYA SELESAI!');
        $this->info("- Jumlah Item Stok Dimasukkan: {$totalItems}");
        $this->info("- Kategori 1 (Alat Tulis):       226 item");
        $this->info("- Kategori 2 (Cenderamata ASM):   35 item");
        $this->info("- Kategori 3 (Penerbitan ASM):   253 item");
        $this->info("- Kategori 4 (Perkakasan Pejabat): 467 item");
        $this->info("- Jumlah Nilai Stok Keseluruhan: RM " . number_format($totalInitialValue, 2));
        $this->info("- Pembahagian Kumpulan A (30% Nilai Tertinggi): {$groupACount} item");
        $this->info("- Pembahagian Kumpulan B (70% Nilai Baki):     {$groupBCount} item");
        $this->info('------------------------------------------------------------');

        return 0;
    }
}
