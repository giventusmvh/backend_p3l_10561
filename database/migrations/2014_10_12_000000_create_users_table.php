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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('id_member');
            $table->string('nama_member');
            $table->string('email_member')->unique();
            $table->date('tgl_lahir_member');
            $table->string('jk_member');
            $table->string('telp_member');
            $table->string('alamat_member');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password_member');
            $table->date('tgl_expired_member');
            $table->float('deposit_uang_member');
            $table->boolean('status_member')->default(false);
            $table->string('api_token');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
