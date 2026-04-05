<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('odps', function (Blueprint $table) {

            $table->id();

            // sumber dari ODC
            $table->foreignId('odc_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // sumber dari ODP lain (ODP turunan)
            $table->foreignId('parent_odp_id')
                ->nullable()
                ->constrained('odps')
                ->nullOnDelete();

            $table->string('nama');

            // jenis splitter
            $table->string('splitter'); // contoh 1:8

            // kapasitas port
            $table->integer('kapasitas');

            // port yang sudah dipakai
            $table->integer('port_terpakai')->default(0);

            $table->text('alamat')->nullable();

            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();

            $table->string('foto')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('odps');
    }
};