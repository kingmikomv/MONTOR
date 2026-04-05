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
        Schema::create('pelanggans', function (Blueprint $table) {
    $table->id();

    $table->foreignId('odp_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('converter_id')->nullable()->constrained()->nullOnDelete();

    $table->string('nama');
    $table->string('username_pppoe')->unique()->nullable();
    $table->string('chat_id')->nullable();

    $table->string('onu_sn')->nullable();
    $table->integer('port');

    // alamat & lokasi rumah
    $table->text('alamat')->nullable();
    $table->decimal('lat', 10, 7)->nullable();
    $table->decimal('lng', 10, 7)->nullable();

    // foto rumah pelanggan
    $table->string('foto')->nullable();

    $table->enum('status', ['aktif','nonaktif'])->default('nonaktif');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggans');
    }
};
