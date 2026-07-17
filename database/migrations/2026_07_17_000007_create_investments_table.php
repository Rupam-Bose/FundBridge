<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investor_id')->constrained('user')->onDelete('cascade');
            $table->foreignId('venture_id')->constrained()->onDelete('cascade');
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 14, 2);
            $table->text('note')->nullable();
            $table->enum('status', ['pending', 'confirmed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
};
