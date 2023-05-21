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
        Schema::create('booking_kelas', function (Blueprint $table) {
            $table->id();
            $table->string('no_booking');  
            $table->unsignedBigInteger('id_depositKelasM');
            $table->foreign('id_depositKelasM')->references('id')->on('deposit_kelasmembers')->onDelete('cascade');
            $table->unsignedBigInteger('id_member');
            $table->foreign('id_member')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('id_jadwalHarian');
            $table->foreign('id_jadwalHarian')->references('id')->on('jadwal_harians')->onDelete('cascade');
            $table->boolean('cancel')->default(false);
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
        Schema::dropIfExists('booking_kelas');
    }
};
