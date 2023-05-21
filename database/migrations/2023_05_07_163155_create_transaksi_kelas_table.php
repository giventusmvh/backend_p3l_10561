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
        Schema::create('transaksi_kelass', function (Blueprint $table) {
            $table->id();
            $table->string('no_struk_kelas');
            $table->unsignedBigInteger('id_promo_kelas');
            $table->foreign('id_promo_kelas')->references('id')->on('promo');
            $table->unsignedBigInteger('id_pegawai_kelas');
            $table->foreign('id_pegawai_kelas')->references('id')->on('pegawais')->cascadeOnUpdate();
            $table->unsignedBigInteger('id_member_kelas');
            $table->foreign('id_member_kelas')->references('id')->on('users')->cascadeOnUpdate();
            $table->unsignedBigInteger('id_kelas');
            $table->foreign('id_kelas')->references('id')->on('kelas');
            $table->dateTime('tgl_TransaksiKelas');
            $table->float('jumlah_bayar_kelas');
            $table->float('bonus_deposit_kelas');
            $table->float('total_deposit_kelas');
            $table->float('sisa_deposit_kelas');
            $table->date('masa_berlaku_depositKelas');
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
        Schema::dropIfExists('transaksi_kelas');
    }
};
