<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluasi_diris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prodi_id')->constrained();
            $table->string('file_data');
            $table->integer('tahun');
            $table->string('status', 100);
            $table->text('keterangan')->nullable(TRUE);
            $table->boolean('feedback')->nullable(TRUE);
            $table->date('tanggal_audit')->nullable(TRUE);
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
        Schema::dropIfExists('evaluasi_diris');
    }
};