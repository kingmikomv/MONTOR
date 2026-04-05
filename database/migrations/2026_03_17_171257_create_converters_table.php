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
        Schema::create('converters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pon_id')->constrained()->cascadeOnDelete();
            $table->string('nama');
            $table->integer('port'); // jumlah port output
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('converters');
    }
};
