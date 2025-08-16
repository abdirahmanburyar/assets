<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_tag')->unique();
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->string('serial_number')->nullable();
            $table->text('item_description');
            $table->foreignId('assigned_to_id')->nullable()->constrained('assignees')->onDelete('set null');
            $table->foreignId('location_id')->constrained()->onDelete('restrict');
            $table->foreignId('sub_location_id')->nullable()->constrained()->onDelete('set null');
            $table->date('acquisition_date');
            $table->enum('status', ['pending', 'in_use', 'maintenance', 'retired', 'rejected'])->default('pending');
            $table->decimal('original_value', 15, 2);
            $table->decimal('current_value', 15, 2);
            $table->decimal('depreciation_rate', 5, 2)->default(0); // Percentage per year
            $table->date('depreciation_start_date');
            $table->date('warranty_expiry')->nullable();
            $table->integer('maintenance_interval_days')->default(0);
            $table->date('next_maintenance_date')->nullable();
            $table->foreignId('funded_source_id')->nullable()->constrained('funded_sources')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'category_id']);
            $table->index(['assigned_to_id', 'status']);
            $table->index(['location_id', 'status']);
            $table->index(['sub_location_id', 'status']);
            $table->index(['warranty_expiry']);
            $table->index(['next_maintenance_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
