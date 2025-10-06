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
        Schema::create('item_request_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_request_id')->constrained()->onDelete('cascade');
            $table->string('status'); // pending, approved, rejected, in_progress, delivered, cancelled
            $table->text('notes')->nullable();
            $table->foreignId('changed_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->timestamps();
            
            // Index for better performance when querying by status
            $table->index(['item_request_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_request_statuses');
    }
};
