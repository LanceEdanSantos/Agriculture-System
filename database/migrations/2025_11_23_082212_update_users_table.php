<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Drop old name column only if it exists
            if (Schema::hasColumn('users', 'name')) {
                $table->dropColumn('name');
            }

            // Add fields only if they do NOT exist
            if (! Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->default('');
            }

            if (! Schema::hasColumn('users', 'middle_name')) {
                $table->string('middle_name')->nullable()->default(null);
            }

            if (! Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->default('');
            }

            if (! Schema::hasColumn('users', 'suffix')) {
                $table->string('suffix')->nullable()->default(null);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Restore name only if it doesn't already exist
            if (! Schema::hasColumn('users', 'name')) {
                $table->string('name');
            }

            // Drop new columns only if they exist
            foreach (['first_name', 'middle_name', 'last_name', 'suffix'] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
