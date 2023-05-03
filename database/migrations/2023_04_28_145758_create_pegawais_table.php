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
        Schema::create('pegawais', function (Blueprint $table) {
            $table->id();
            $table->string('id_pegawai');
            $table->string('nama_pegawai');
            $table->string('alamat_pegawai');
            $table->string('telp_pegawai');
            $table->string('role');
            $table->string('password_pegawai');
            $table->date('tgl_lahir_pegawai');
            $table->string('api_token');
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
        Schema::dropIfExists('pegawais');
    }
};
