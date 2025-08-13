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
        Schema::table('purchase_request_items', function (Blueprint $table) {
            $table->foreignId('inventory_item_id')->nullable()->constrained('inventory_items')->onDelete('set null');
            $table->string('item_code')->nullable()->after('item_no');
            $table->boolean('is_custom_item')->default(false)->after('category_total');
            $table->boolean('add_to_inventory')->default(false)->after('is_custom_item');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_request_items', function (Blueprint $table) {
            $table->dropForeign(['inventory_item_id']);
            $table->dropColumn(['inventory_item_id', 'item_code', 'is_custom_item', 'add_to_inventory']);
        });
    }
};