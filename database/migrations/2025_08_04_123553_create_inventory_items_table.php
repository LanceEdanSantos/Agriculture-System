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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('category')->nullable(); // Legacy field for backward compatibility
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->string('unit')->nullable(); // Legacy field for backward compatibility
            $table->foreignId('unit_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->decimal('average_unit_cost', 10, 2)->default(0);
            $table->integer('current_stock')->default(0);
            $table->integer('minimum_stock')->default(0);
            $table->integer('total_purchased')->default(0);
            $table->string('item_code')->unique()->nullable();
            $table->string('supplier')->nullable(); // Legacy field for backward compatibility
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->string('last_supplier')->nullable();
            $table->date('last_purchase_date')->nullable();
            $table->date('expiration_date')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'inactive', 'discontinued'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
