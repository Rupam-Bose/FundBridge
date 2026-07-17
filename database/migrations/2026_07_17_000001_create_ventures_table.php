<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('user')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('sector')->nullable(); // e.g. FinTech, HealthTech
            $table->string('stage')->nullable();  // e.g. Seed, Series A
            $table->decimal('goal_amount', 15, 2)->default(0);
            $table->decimal('raised_amount', 15, 2)->default(0);
            $table->enum('status', ['active', 'completed', 'paused', 'draft'])->default('draft');
            $table->string('logo_path')->nullable();
            $table->string('pitch_deck_path')->nullable();
            $table->integer('views')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventures');
    }
};
