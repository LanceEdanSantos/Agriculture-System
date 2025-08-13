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
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->string('province');
            $table->boolean('lgu')->default(false);
            $table->string('responsibility_center');
            $table->string('account_code');
            $table->string('department');
            $table->string('pr_no')->nullable();
            $table->string('sai_no')->nullable();
            $table->date('date');
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->string('delivery_place');
            $table->string('delivery_date_terms');
            $table->string('prepared_by');
            $table->string('certified_by');
            $table->string('requested_by');
            $table->json('approved_by');
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'completed'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
};
