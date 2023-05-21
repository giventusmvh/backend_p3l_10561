<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('izin_instrukturs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_instruktur');
            $table->foreign('id_instruktur')->references('id')->on('instrukturs');
            $table->unsignedBigInteger('id_instruktur_pengganti');
            $table->foreign('id_instruktur_pengganti')->references('id')->on('instrukturs');
            $table->date('tgl_izin');
            $table->boolean('konfirmasi')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('izin_instrukturs');
    }
};
