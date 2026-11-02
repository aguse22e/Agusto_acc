<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJurnalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jurnal', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('tanggal');
            $table->text('keterangan');
            $table->string('kode_transaksi_kas')->nullable();
            $table->foreign('kode_transaksi_kas')->references('kode_transaksi_kas')->on('transaksi_kas');
            $table->string('kode_transaksi_bank')->nullable();
            $table->string('kode_memorial')->nullable();
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
        Schema::dropIfExists('jurnal');
    }
}
