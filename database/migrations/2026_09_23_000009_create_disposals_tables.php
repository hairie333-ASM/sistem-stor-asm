<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // KEW.PS-19 Lantikan, KEW.PS-20 Laporan Lembaga, KEW.PS-21 Kelulusan, KEW.PS-22 Saksi Musnah, KEW.PS-23 Sijil Pelupusan
        Schema::create('disposals', function (Blueprint $table) {
            $table->id();
            $table->string('disposal_number')->unique(); // KEW.PS-20: e.g. PS20/ASM/2026/0001
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->string('committee_appointment_ref')->nullable(); // KEW.PS-19
            $table->enum('disposal_method', [
                'SALE',             // Jualan (Tender / Sebut Harga / Lelong)
                'SCRAP',            // Jualan Sisa
                'SCHEDULED_WASTE',  // Buangan Terjadual / E-Waste
                'EXCHANGE',         // Tukar Barang / Perkhidmatan
                'TRADE_IN',         // Tukar Beli
                'GIFT',             // Hadiah / Pindahan ke agensi lain
                'DESTROY'           // Musnah (Tanam / Bakar / Buang)
            ])->default('DESTROY');
            
            $table->enum('status', [
                'PROPOSED',
                'INSPECTED',
                'APPROVED',
                'IN_PROGRESS',
                'COMPLETED',
                'CANCELLED'
            ])->default('PROPOSED');
            
            $table->string('approval_reference')->nullable(); // KEW.PS-21 Surat Kelulusan
            $table->foreignId('approver_id')->nullable()->constrained('users')->nullOnDelete(); // Kuasa Melulus
            $table->timestamp('approved_at')->nullable();
            
            $table->string('witness_cert_number')->nullable();  // KEW.PS-22 Sijil Saksi Pemusnahan
            $table->string('completion_cert_number')->nullable();// KEW.PS-23 Sijil Pelupusan
            $table->timestamp('completed_at')->nullable();
            
            $table->decimal('total_original_value', 14, 2)->default(0);
            $table->decimal('total_revenue', 14, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('disposal_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disposal_id')->constrained('disposals')->cascadeOnDelete();
            $table->foreignId('stock_item_id')->constrained('stock_items')->cascadeOnDelete();
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('total_price', 14, 2)->default(0);
            $table->enum('condition', [
                'USANG',              // Obsolete
                'ROSAK',              // Damaged
                'EXPIRED',            // Expired
                'TIDAK_DIPERLUKAN',   // No longer required
                'LEBIHAN',            // Excess
                'TIDAK_EKONOMI',      // Not economical
                'LAIN_LAIN'
            ])->default('USANG');
            $table->text('justification')->nullable();
            $table->string('recommended_method')->nullable();
            $table->decimal('actual_revenue', 14, 2)->default(0);
            $table->string('status')->default('PROPOSED');
            $table->timestamps();
        });

        Schema::create('disposal_committees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disposal_id')->constrained('disposals')->cascadeOnDelete();
            $table->string('officer_name');
            $table->string('position');
            $table->string('department')->nullable();
            $table->enum('role', ['PENGERUSI', 'AHLI', 'SAKSI'])->default('AHLI');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disposal_committees');
        Schema::dropIfExists('disposal_items');
        Schema::dropIfExists('disposals');
    }
};
