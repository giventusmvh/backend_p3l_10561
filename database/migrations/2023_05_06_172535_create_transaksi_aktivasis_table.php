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
        Schema::create('transaksi_aktivasis', function (Blueprint $table) {
            $table->id();
            $table->string('no_struk');
            $table->unsignedBigInteger('id_pegawai_aktivasi');
            $table->foreign('id_pegawai_aktivasi')->references('id')->on('pegawais')->onDelete('cascade');
            $table->unsignedBigInteger('id_member_aktivasi');
            $table->foreign('id_member_aktivasi')->references('id')->on('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->dateTime('tgl_TransaksiAktivasi');
            $table->float('jumlah_bayar_aktivasi');
            $table->date('masa_berlaku_aktivasi');
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
        Schema::dropIfExists('transaksi_aktivasis');
    }
};
