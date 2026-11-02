<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksiKasDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksi_kas_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kode_transaksi_kas',20);
            $table->string('kode_lawan',15);
            $table->decimal('subtotal',13,2);
            $table->text('keterangan')->nullable();
            $table->foreign('kode_transaksi_kas')->references('kode_transaksi_kas')->on('transaksi_kas');
            $table->foreign('kode_lawan')->references('kode_akun')->on('kode_akun');
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
        Schema::dropIfExists('transaksi_kas_detail');
    }
}
