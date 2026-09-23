<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_units', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('symbol')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_items', function (Blueprint $table) {
            $table->id();
            $table->string('kad_no')->nullable(); // No. Kad KEW.PS-3
            $table->string('stock_code')->unique(); // No. Kod Stok
            $table->text('description'); // Perihal Stok
            $table->foreignId('category_id')->constrained('stock_categories')->cascadeOnDelete();
            $table->enum('stock_group', ['A', 'B'])->default('B'); // TPS Group A (30%) / B (70%)
            $table->enum('movement', ['CEPAT', 'PERLAHAN'])->default('CEPAT'); // Cepat / Perlahan
            $table->foreignId('uom_id')->constrained('stock_units')->cascadeOnDelete();
            $table->foreignId('default_location_id')->nullable()->constrained('locations')->nullOnDelete();
            
            // Stock Levels (3-2-1 Month rule)
            $table->decimal('min_level', 12, 2)->default(0);     // Paras Minimum (1 Bulan)
            $table->decimal('reorder_level', 12, 2)->default(0); // Paras Menokok (2 Bulan)
            $table->decimal('max_level', 12, 2)->default(0);     // Paras Maksimum (3 Bulan)
            
            $table->decimal('unit_price', 14, 2)->default(0);    // Harga Seunit Semasa
            $table->string('supplier_name')->nullable();
            
            // Control flags
            $table->boolean('is_expiry_controlled')->default(false);
            $table->boolean('is_batch_controlled')->default(false);
            $table->boolean('is_serial_controlled')->default(false);
            
            // Strict Stock Classification Quantities
            $table->decimal('current_quantity', 12, 2)->default(0);    // Available (Boleh dikeluarkan)
            $table->decimal('reserved_quantity', 12, 2)->default(0);   // Diperuntukkan
            $table->decimal('quarantine_quantity', 12, 2)->default(0); // Kuarantin (belum diperiksa)
            $table->decimal('damaged_quantity', 12, 2)->default(0);    // Rosak
            $table->decimal('disposal_quantity', 12, 2)->default(0);   // Menunggu Pelupusan
            $table->decimal('written_off_quantity', 12, 2)->default(0);// Hapus Kira
            
            $table->enum('status', ['AVAILABLE', 'RESERVED', 'QUARANTINE', 'DAMAGED', 'EXPIRED', 'DISPOSAL', 'WRITTEN_OFF', 'INACTIVE'])->default('AVAILABLE');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_item_id')->constrained('stock_items')->cascadeOnDelete();
            $table->string('batch_number');
            $table->date('expiry_date')->nullable();
            $table->decimal('quantity', 12, 2)->default(0);
            $table->decimal('remaining_quantity', 12, 2)->default(0);
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->string('status')->default('AVAILABLE'); // AVAILABLE, EXPIRED, QUARANTINE
            $table->timestamps();
        });

        Schema::create('stock_serial_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_item_id')->constrained('stock_items')->cascadeOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('stock_batches')->nullOnDelete();
            $table->string('serial_number');
            $table->string('status')->default('AVAILABLE'); // AVAILABLE, ISSUED, DISPOSED, LOST
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_serial_numbers');
        Schema::dropIfExists('stock_batches');
        Schema::dropIfExists('stock_items');
        Schema::dropIfExists('stock_units');
        Schema::dropIfExists('stock_categories');
    }
};
