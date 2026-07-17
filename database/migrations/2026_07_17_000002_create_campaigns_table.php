<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venture_id')->constrained('ventures')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('goal', 15, 2)->default(0);
            $table->decimal('raised', 15, 2)->default(0);
            $table->date('deadline')->nullable();
            $table->enum('status', ['active', 'completed', 'paused'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
