<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite does not support ALTER COLUMN for ENUMs directly.
        // We add the admin role by recreating the column.
        // For SQLite (dev), we can store as string. Update check constraint manually if needed.

        Schema::table('user', function (Blueprint $table) {
            // Add avatar column
            $table->string('avatar')->nullable()->after('company_name');
            // Add bio column
            $table->text('bio')->nullable()->after('avatar');
        });
    }

    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'bio']);
        });
    }
};
