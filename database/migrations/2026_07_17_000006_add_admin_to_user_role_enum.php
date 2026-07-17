<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL supports modifying ENUM columns
        DB::statement("ALTER TABLE `user` MODIFY COLUMN `role` ENUM('founder','investor','admin') NOT NULL DEFAULT 'founder'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `user` MODIFY COLUMN `role` ENUM('founder','investor') NOT NULL DEFAULT 'founder'");
    }
};
