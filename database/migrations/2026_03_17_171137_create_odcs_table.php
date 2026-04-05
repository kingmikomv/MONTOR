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
        Schema::create('odcs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('olt_id')->constrained()->cascadeOnDelete();
    $table->foreignId('pon_id')->constrained()->cascadeOnDelete();
    $table->string('nama');
    $table->string('splitter'); // 1:4 dll
    $table->integer('kapasitas'); // hasil dari splitter (contoh 4)
    $table->integer('port_terpakai')->default(0);
    $table->text('alamat')->nullable();
    $table->decimal('lat', 10, 7)->nullable();
    $table->decimal('lng', 10, 7)->nullable();
    $table->string('foto')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('odcs');
    }
};
