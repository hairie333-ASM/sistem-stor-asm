<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // AM 6.7 Keselamatan & Kebersihan
        Schema::create('safety_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->date('inspection_date');
            $table->foreignId('inspector_id')->constrained('users')->cascadeOnDelete();
            $table->enum('category', [
                'SECURITY',      // Kawalan Akses, Kunci, CCTV, Penggera
                'FIRE_SAFETY',   // Alat Pemadam Api, Gelung Hos, Pintu Kecemasan
                'CLEANLINESS',   // Jadual Pembersihan, Susun Atur
                'PEST_CONTROL'   // Kawalan Serangga/Makhluk Perosak
            ]);
            $table->json('checklist_items')->nullable();
            $table->integer('score')->default(100);
            $table->enum('status', ['PASS', 'ACTION_REQUIRED'])->default('PASS');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // Audit Trail (Critical: immutable action logs)
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('role_name')->nullable();
            $table->string('action'); // CREATE, UPDATE, DELETE, APPROVE, REJECT, RECEIVE, ISSUE, etc.
            $table->string('module'); // Stock, Receiving, Issue, Transfer, Verification, Disposal, Loss
            $table->string('record_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // System Settings
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('category')->default('GENERAL');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Attachments
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->string('attachable_type');
            $table->unsignedBigInteger('attachable_id');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type')->nullable();
            $table->integer('file_size')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['attachable_type', 'attachable_id']);
        });

        // Notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('type'); // STOCK_MIN, EXPIRY, PENDING_APPROVAL, etc.
            $table->string('title');
            $table->text('message');
            $table->string('link_url')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('attachments');
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('safety_inspections');
    }
};
