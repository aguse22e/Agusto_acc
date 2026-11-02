<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksiBankDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksi_bank_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kode_transaksi_bank',20);
            $table->string('kode_lawan',15);
            $table->decimal('subtotal',13,2);
            $table->text('keterangan')->nullable();
            $table->foreign('kode_transaksi_bank')->references('kode_transaksi_bank')->on('transaksi_bank');
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
        Schema::dropIfExists('transaksi_bank_detail');
    }
}
