<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investor_interests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investor_id')->constrained('user')->onDelete('cascade');
            $table->foreignId('venture_id')->constrained('ventures')->onDelete('cascade');
            $table->enum('interest_level', ['low', 'medium', 'high'])->default('medium');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['investor_id', 'venture_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investor_interests');
    }
};
