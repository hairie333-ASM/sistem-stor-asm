<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // KEW.PS-10 Pelantikan, KEW.PS-11 Jadual, KEW.PS-12 Laporan, KEW.PS-13 Sijil
        Schema::create('stock_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('verification_number')->unique(); // e.g. PS11/ASM/2026/0001
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->year('year');
            $table->string('appointment_letter_ref')->nullable(); // KEW.PS-10 No. Rujukan
            $table->date('scheduled_date');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            
            // Minimum 2 Verifiers (not from the store being verified)
            $table->foreignId('verifier_1_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('verifier_2_id')->constrained('users')->cascadeOnDelete();
            
            $table->boolean('is_frozen')->default(false); // Freeze transactions during verification
            $table->enum('status', ['SCHEDULED', 'IN_PROGRESS', 'COMPLETED', 'APPROVED'])->default('SCHEDULED');
            
            $table->string('report_number')->nullable(); // KEW.PS-12 Laporan
            $table->string('cert_number')->nullable();   // KEW.PS-13 Sijil
            
            $table->foreignId('approval_officer_id')->nullable()->constrained('users')->nullOnDelete(); // Ketua Jabatan
            $table->timestamp('approved_at')->nullable();
            
            $table->text('findings_summary')->nullable();
            $table->text('corrective_actions')->nullable();
            $table->timestamps();
        });

        Schema::create('verification_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('verification_id')->constrained('stock_verifications')->cascadeOnDelete();
            $table->foreignId('stock_item_id')->constrained('stock_items')->cascadeOnDelete();
            $table->decimal('system_quantity', 12, 2);
            $table->decimal('physical_quantity', 12, 2)->default(0);
            $table->decimal('variance_quantity', 12, 2)->default(0); // Physical - System
            $table->decimal('surplus_quantity', 12, 2)->default(0);  // Lebih
            $table->decimal('shortage_quantity', 12, 2)->default(0); // Kurang
            $table->decimal('damaged_quantity', 12, 2)->default(0);  // Rosak
            $table->decimal('obsolete_quantity', 12, 2)->default(0); // Usang
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('variance_value', 14, 2)->default(0);
            $table->enum('condition_status', ['BAIK', 'ROSAK', 'USANG', 'TIDAK_BERGERAK'])->default('BAIK');
            $table->string('remarks')->nullable();
            $table->timestamps();
        });

        // KEW.PS-15 Pelarasan Stok & KEW.PS-16 Perakuan Pelarasan
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('adjustment_number')->unique(); // KEW.PS-15: e.g. PS15/ASM/2026/0001
            $table->foreignId('verification_id')->nullable()->constrained('stock_verifications')->nullOnDelete();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->enum('reason', [
                'RECORDING_ERROR',       // Kesilapan Merekod
                'PHYSICAL_DISCREPANCY',  // Percanggahan Fizikal
                'DAMAGE',                // Kerosakan Semasa Simpanan
                'APPROVED_VERIFICATION', // Hasil Verifikasi Stor Diluluskan
                'OTHER'                  // Sebab Lain yang Diluluskan
            ]);
            $table->enum('status', ['DRAFT', 'SUBMITTED', 'APPROVED', 'REJECTED'])->default('DRAFT');
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approver_id')->nullable()->constrained('users')->nullOnDelete(); // Ketua Jabatan
            $table->timestamp('approved_at')->nullable();
            $table->string('perakuan_number')->nullable(); // KEW.PS-16
            $table->text('approval_remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_adjustment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_adjustment_id')->constrained('stock_adjustments')->cascadeOnDelete();
            $table->foreignId('stock_item_id')->constrained('stock_items')->cascadeOnDelete();
            $table->decimal('current_quantity', 12, 2);
            $table->decimal('adjustment_quantity', 12, 2); // +/- quantity
            $table->decimal('new_quantity', 12, 2);
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('total_variance_value', 14, 2)->default(0);
            $table->string('reason_detail')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustment_items');
        Schema::dropIfExists('stock_adjustments');
        Schema::dropIfExists('verification_items');
        Schema::dropIfExists('stock_verifications');
    }
};
