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
            $table->id('id_user');
            $table->index('id_role');
            $table->tinyInteger('id_role');
            $table->foreign('id_role')->references('id_role')->on('roles');
            $table->index('id_jurusan');
            $table->tinyInteger('id_jurusan')->nullable();
            $table->foreign('id_jurusan')->references('id_jurusan')->on('jurusan');
            $table->index('id_prodi');
            $table->tinyInteger('id_prodi')->nullable();
            $table->foreign('id_prodi')->references('id_prodi')->on('prodi');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
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
