<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // KEW.PS-32 Laporan Awal, KEW.PS-33 Jawatankuasa Penyiasat, KEW.PS-34 Laporan Akhir, KEW.PS-35 Sijil Hapus Kira, KEW.PS-36 Laporan Tindakan Surcaj
        Schema::create('loss_cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_number')->unique(); // KEW.PS-32: e.g. PS32/ASM/2026/0001
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->date('incident_date');
            $table->date('discovery_date');
            $table->text('description'); // Perihal Kehilangan
            
            // Police report
            $table->string('police_report_no')->nullable();
            $table->date('police_report_date')->nullable();
            $table->string('police_action_status')->nullable();
            
            // Workflow stages
            $table->string('investigation_committee_ref')->nullable(); // KEW.PS-33
            $table->string('final_report_ref')->nullable();             // KEW.PS-34
            $table->string('write_off_cert_number')->nullable();         // KEW.PS-35 Sijil Hapus Kira
            
            $table->enum('status', [
                'REPORTED',                  // Laporan Awal Dihantar
                'INVESTIGATING',             // Jawatankuasa Sedang Menyiasat
                'SUBMITTED_FOR_WRITE_OFF',   // Laporan Akhir Dihantar
                'APPROVED',                  // Diluluskan Hapus Kira
                'REJECTED',                  // Ditolak
                'SURCHARGE_RECOMMENDED',     // Tindakan Surcaj Disyorkan (KEW.PS-36)
                'CLOSED'                     // Selesai
            ])->default('REPORTED');
            
            $table->date('authority_approval_date')->nullable();
            $table->foreignId('approver_id')->nullable()->constrained('users')->nullOnDelete(); // Kuasa Melulus
            
            // Surcharge & Disciplinary (KEW.PS-36)
            $table->boolean('surcharge_recommended')->default(false);
            $table->text('surcharge_details')->nullable();
            $table->decimal('total_loss_value', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('loss_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loss_case_id')->constrained('loss_cases')->cascadeOnDelete();
            $table->foreignId('stock_item_id')->constrained('stock_items')->cascadeOnDelete();
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('total_value', 14, 2)->default(0);
            $table->text('circumstances')->nullable(); // Cara kehilangan berlaku
            $table->timestamps();
        });

        Schema::create('investigation_committees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loss_case_id')->constrained('loss_cases')->cascadeOnDelete();
            $table->string('officer_name');
            $table->string('position');
            $table->string('department')->nullable();
            $table->enum('role', ['PENGERUSI', 'AHLI'])->default('AHLI');
            $table->text('findings')->nullable();
            $table->text('recommendations')->nullable();
            $table->date('report_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investigation_committees');
        Schema::dropIfExists('loss_items');
        Schema::dropIfExists('loss_cases');
    }
};
