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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->json('contact_persons')->nullable(); // Array of contact persons with their details
            $table->json('phone_numbers')->nullable(); // Array of phone numbers
            $table->json('email_addresses')->nullable(); // Array of email addresses
            $table->text('address')->nullable();
            $table->string('website')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('business_license')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
