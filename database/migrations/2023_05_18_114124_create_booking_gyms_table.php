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
        Schema::create('booking_gyms', function (Blueprint $table) {
            $table->id();
            $table->string('no_booking');  
            $table->unsignedBigInteger('id_member');
            $table->foreign('id_member')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('id_sesi');
            $table->foreign('id_sesi')->references('id')->on('sesi_gyms')->onDelete('cascade');
            $table->date('tgl_booking');
            $table->datetime('waktu_presensi_gym');
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
        Schema::dropIfExists('booking_gyms');
    }
};