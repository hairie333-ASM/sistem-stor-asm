<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique(); // e.g. PS8/ASM/2026/0001 or PS7/ASM/2026/0001
            $table->enum('form_type', ['KEW.PS-8', 'KEW.PS-7'])->default('KEW.PS-8');
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('requesting_store_id')->nullable()->constrained('stores')->nullOnDelete(); // for KEW.PS-7
            $table->string('department')->nullable();
            $table->text('purpose')->nullable();
            $table->enum('priority', ['NORMAL', 'URGENT'])->default('NORMAL');
            
            // Workflow status
            $table->enum('status', [
                'DRAFT',
                'SUBMITTED',
                'UNDER_REVIEW',
                'APPROVED',
                'PARTIALLY_APPROVED',
                'REJECTED',
                'ISSUED',
                'COMPLETED',
                'CANCELLED'
            ])->default('DRAFT');
            
            // Approval stage
            $table->foreignId('approver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_remarks')->nullable();
            
            // Issue stage (Pegawai Stor)
            $table->foreignId('issuer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('issued_at')->nullable();
            
            // Confirmation stage (Penerima)
            $table->foreignId('recipient_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('received_at')->nullable();
            $table->text('recipient_notes')->nullable();
            
            $table->timestamps();
        });

        Schema::create('stock_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_request_id')->constrained('stock_requests')->cascadeOnDelete();
            $table->foreignId('stock_item_id')->constrained('stock_items')->cascadeOnDelete();
            $table->decimal('requested_quantity', 12, 2);
            $table->decimal('approved_quantity', 12, 2)->default(0);
            $table->decimal('issued_quantity', 12, 2)->default(0);
            $table->foreignId('batch_id')->nullable()->constrained('stock_batches')->nullOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->string('remarks')->nullable();
            $table->timestamps();
        });

        // KEW.PS-9 Borang Pembungkusan
        Schema::create('packings', function (Blueprint $table) {
            $table->id();
            $table->string('packing_number')->unique(); // e.g. PS9/ASM/2026/0001
            $table->foreignId('stock_request_id')->constrained('stock_requests')->cascadeOnDelete();
            $table->string('package_number')->default('BKG-01');
            $table->string('package_type')->default('KOTAK');
            $table->decimal('weight_kg', 8, 2)->nullable();
            $table->string('dimensions')->nullable();
            $table->string('sender_name');
            $table->string('receiver_name');
            $table->text('delivery_address');
            $table->json('handling_instructions')->nullable(); // Fragile, Keep Dry, etc.
            $table->foreignId('packing_officer_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // Stock Returns
        Schema::create('stock_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_number')->unique(); // e.g. RET/ASM/2026/0001
            $table->foreignId('stock_request_id')->nullable()->constrained('stock_requests')->nullOnDelete();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('return_type', ['FULL', 'PARTIAL', 'UNUSED', 'DAMAGED'])->default('UNUSED');
            $table->enum('status', ['PENDING_INSPECTION', 'ACCEPTED', 'REJECTED'])->default('PENDING_INSPECTION');
            $table->foreignId('inspector_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('inspected_at')->nullable();
            $table->text('inspection_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_return_id')->constrained('stock_returns')->cascadeOnDelete();
            $table->foreignId('stock_item_id')->constrained('stock_items')->cascadeOnDelete();
            $table->decimal('returned_quantity', 12, 2);
            $table->decimal('accepted_quantity', 12, 2)->default(0);
            $table->enum('condition', ['GOOD', 'DAMAGED'])->default('GOOD');
            $table->boolean('is_restocked')->default(false);
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_return_items');
        Schema::dropIfExists('stock_returns');
        Schema::dropIfExists('packings');
        Schema::dropIfExists('stock_request_items');
        Schema::dropIfExists('stock_requests');
    }
};
