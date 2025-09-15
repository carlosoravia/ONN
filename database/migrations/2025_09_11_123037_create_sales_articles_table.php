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
        Schema::create('sales_articles', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('descrizione');
            $table->string('cat_omogenea')->nullable();
            $table->string('desc_cat')->nullable();
            $table->string('reparto')->nullable();
            $table->string('natura')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_articles');
    }
};
