<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique(); // e.g. TXN/2026/0001
            $table->enum('transaction_type', [
                'RECEIPT',      // Terimaan
                'ISSUE',        // Pengeluaran
                'TRANSFER_IN',  // Pindahan Masuk
                'TRANSFER_OUT', // Pindahan Keluar
                'RETURN',       // Pemulangan
                'ADJUSTMENT',   // Pelarasan (+/-)
                'DISPOSAL',     // Pelupusan (-)
                'WRITE_OFF'     // Hapus Kira (-)
            ]);
            $table->string('kew_ps_type')->nullable(); // KEW.PS-1, KEW.PS-7, KEW.PS-8, KEW.PS-15, KEW.PS-17, KEW.PS-20, KEW.PS-35
            $table->string('reference_number')->index(); // BTB No, Issue No, etc.
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->foreignId('destination_store_id')->nullable()->constrained('stores')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Pegawai yang merekod
            $table->string('party_name')->nullable(); // Pembekal / Pemohon / Penerima / Lembaga
            $table->date('transaction_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('stock_transactions')->cascadeOnDelete();
            $table->foreignId('stock_item_id')->constrained('stock_items')->cascadeOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('stock_batches')->nullOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->enum('movement_type', ['IN', 'OUT']);
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('total_price', 14, 2)->default(0);
            
            // Historical running balances for KEW.PS-3 Bahagian B
            $table->decimal('balance_quantity_before', 12, 2)->default(0);
            $table->decimal('balance_quantity_after', 12, 2)->default(0);
            $table->decimal('balance_value_after', 14, 2)->default(0);
            
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transaction_items');
        Schema::dropIfExists('stock_transactions');
    }
};
