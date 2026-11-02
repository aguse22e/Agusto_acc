<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSequencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sequences', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10);
            $table->year('tahun');
            $table->string('bulan', 2);
            $table->tinyInteger('seq_length');
            $table->tinyInteger('seq_no');
            $table->string('kode_akun', 15)->nullable();
            $table->timestamps();

            $table->foreign('kode_akun')->references('kode_akun')->on('kode_akun');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sequences');
    }
}
