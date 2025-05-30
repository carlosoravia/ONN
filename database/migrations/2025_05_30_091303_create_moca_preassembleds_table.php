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
        Schema::create('moca_preassembleds', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->text('description');
            $table->text('padre_description');
            $table->text('actvity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moca_preassembleds');
    }
};
