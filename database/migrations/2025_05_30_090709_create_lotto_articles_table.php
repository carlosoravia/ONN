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
        Schema::create('lotto_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lotto_id')->constrained('lottos')->onDelete('cascade');
            $table->foreignId('article_id')->constrained('articles')->onDelete('cascade');
            $table->string('supplier_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lotto_articles');
    }
};
