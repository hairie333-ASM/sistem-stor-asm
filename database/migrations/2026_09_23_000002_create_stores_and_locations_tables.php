<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('store_type')->default('UTAMA'); // PUSAT, UTAMA, UNIT
            $table->text('address')->nullable();
            $table->foreignId('officer_in_charge_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('store_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('store_sections')->nullOnDelete();
            $table->string('row');     // Baris
            $table->string('rack');    // Rak
            $table->string('level');   // Tingkat
            $table->string('bin');     // Petak
            $table->string('full_code')->unique(); // e.g. A-01-RB01-01-01
            $table->string('barcode')->nullable();
            $table->integer('capacity')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
        Schema::dropIfExists('store_sections');
        Schema::dropIfExists('stores');
    }
};
