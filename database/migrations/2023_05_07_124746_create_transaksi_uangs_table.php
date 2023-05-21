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
        Schema::create('transaksi_uangs', function (Blueprint $table) {
            $table->id();
            $table->string('no_struk_uang');
            $table->unsignedBigInteger('id_promo_uang');
            $table->foreign('id_promo_uang')->references('id')->on('promo');
            $table->unsignedBigInteger('id_pegawai_uang');
            $table->foreign('id_pegawai_uang')->references('id')->on('pegawais')->cascadeOnUpdate();
            $table->unsignedBigInteger('id_member_uang');
            $table->foreign('id_member_uang')->references('id')->on('users')->cascadeOnUpdate();
            $table->dateTime('tgl_TransaksiUang');
            $table->float('jumlah_bayar_uang');
            $table->float('bonus_deposit_uang');
            $table->float('total_deposit_uang');
            $table->float('sisa_deposit_uang');
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
        Schema::dropIfExists('transaksi_uangs');
    }
};
