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
        Schema::create('lotto_preassmbleds_quantities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lotto_id')->constrained('lottos')->onDelete('cascade');
            $table->foreignId('preassembled_id');
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lotto_preassmbleds_quantities');
    }
};
