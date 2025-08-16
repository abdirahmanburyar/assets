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
        Schema::create('asset_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->string('action_type'); // status_change, location_transfer, assignment, maintenance, depreciation_update
            $table->string('field_name')->nullable(); // The field that was changed
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->text('description');
            $table->foreignId('changed_by')->constrained('users')->onDelete('restrict');
            $table->json('metadata')->nullable(); // Additional data about the change
            $table->timestamps();
            
            $table->index(['asset_id', 'action_type']);
            $table->index(['changed_by']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_histories');
    }
};
