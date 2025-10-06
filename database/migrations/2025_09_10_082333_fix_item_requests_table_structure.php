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
        // Schema::dropIfExists('item_requests');
        
        // Schema::create('item_requests', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('user_id')->constrained()->onDelete('cascade');
        //     $table->foreignId('farm_id')->constrained()->onDelete('cascade');
        //     $table->foreignId('inventory_item_id')->constrained()->onDelete('cascade');
        //     $table->decimal('quantity', 10, 2);
        //     $table->text('notes')->nullable();
        //     $table->string('status')->default('pending');
        //     $table->dateTime('requested_at');
        //     $table->dateTime('approved_at')->nullable();
        //     $table->dateTime('delivered_at')->nullable();
        //     $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
        //     $table->text('rejection_reason')->nullable();
        //     $table->timestamps();
            
        //     // Indexes for better performance
        //     $table->index(['farm_id', 'status']);
        //     $table->index(['user_id', 'status']);
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('item_requests');
    }
};
