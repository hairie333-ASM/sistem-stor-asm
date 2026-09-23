<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receivings', function (Blueprint $table) {
            $table->id();
            $table->string('btb_number')->unique(); // KEW.PS-1: e.g. BTB/ASM/2026/0001
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->string('supplier_name');
            $table->text('supplier_address')->nullable();
            $table->enum('receipt_type', ['PURCHASE', 'TRANSFER', 'GIFT', 'SEIZURE', 'OTHER'])->default('PURCHASE');
            $table->string('po_contract_number')->nullable(); // No. Pesanan Tempatan / Kontrak
            $table->date('po_contract_date')->nullable();
            $table->string('delivery_order_number')->nullable(); // No. Nota Hantaran (DO)
            $table->date('delivery_order_date')->nullable();
            $table->string('carrier_info')->nullable(); // Maklumat Pengangkutan
            $table->enum('status', ['DRAFT', 'INSPECTED', 'ACCEPTED', 'PARTIALLY_REJECTED', 'REJECTED'])->default('DRAFT');
            $table->foreignId('receiving_officer_id')->constrained('users')->cascadeOnDelete(); // Pegawai Penerima
            $table->foreignId('technical_officer_id')->nullable()->constrained('users')->nullOnDelete(); // Pegawai Teknikal jika perlu
            $table->date('inspection_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('receiving_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receiving_id')->constrained('receivings')->cascadeOnDelete();
            $table->foreignId('stock_item_id')->constrained('stock_items')->cascadeOnDelete();
            $table->decimal('ordered_quantity', 12, 2)->default(0);
            $table->decimal('do_quantity', 12, 2)->default(0);
            $table->decimal('received_quantity', 12, 2)->default(0);
            $table->decimal('accepted_quantity', 12, 2)->default(0);
            $table->decimal('rejected_quantity', 12, 2)->default(0);
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('total_price', 14, 2)->default(0);
            $table->string('batch_number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->enum('status', ['PENDING', 'ACCEPTED', 'REJECTED', 'PARTIAL'])->default('PENDING');
            $table->string('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('rejections', function (Blueprint $table) {
            $table->id();
            $table->string('bpb_number')->unique(); // KEW.PS-2: e.g. BPB/ASM/2026/0001
            $table->foreignId('receiving_id')->constrained('receivings')->cascadeOnDelete();
            $table->string('supplier_name');
            $table->string('delivery_order_number')->nullable();
            $table->date('rejection_date');
            $table->foreignId('officer_id')->constrained('users')->cascadeOnDelete();
            $table->string('supplier_agent_name')->nullable();
            $table->date('supplier_acknowledgement_date')->nullable();
            $table->enum('status', ['PENDING_ACK', 'ACKNOWLEDGED', 'RESOLVED'])->default('PENDING_ACK');
            $table->timestamps();
        });

        Schema::create('rejection_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rejection_id')->constrained('rejections')->cascadeOnDelete();
            $table->foreignId('receiving_item_id')->constrained('receiving_items')->cascadeOnDelete();
            $table->foreignId('stock_item_id')->constrained('stock_items')->cascadeOnDelete();
            $table->decimal('rejected_quantity', 12, 2);
            $table->enum('rejection_reason', [
                'DAMAGED',                  // Rosak
                'QUANTITY_LESS',            // Kuantiti Kurang
                'QUANTITY_MORE',            // Kuantiti Lebih
                'WRONG_ITEM',               // Barang Tidak Sama
                'SPEC_MISMATCH',            // Tidak Ikut Spesifikasi
                'QUALITY_ISSUE',            // Isu Kualiti
                'OTHER'                     // Lain-lain
            ]);
            $table->string('action_to_take')->nullable(); // Penggantian / Pembatalan Pesanan
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rejection_items');
        Schema::dropIfExists('rejections');
        Schema::dropIfExists('receiving_items');
        Schema::dropIfExists('receivings');
    }
};
