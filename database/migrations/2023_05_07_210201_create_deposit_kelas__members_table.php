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
        Schema::create('deposit_kelasMembers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_member');
            $table->foreign('id_member')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('id_kelas');
            $table->foreign('id_kelas')->references('id')->on('kelas')->onDelete('cascade');
            $table->date('masa_berlaku_depositK');
            $table->float('sisa_depositK');
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
        Schema::dropIfExists('deposit_kelas__members');
    }
};
