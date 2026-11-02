<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksiKasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksi_kas', function (Blueprint $table) {
            $table->string('kode_transaksi_kas',20)->primary();
            $table->date('tanggal');
            $table->string('akun_kode');
            $table->foreign('akun_kode')->references('kode_akun')->on('kode_akun');
            $table->enum('tipe',['Masuk','Keluar']);
            $table->decimal('total',13,2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaksi_kas');
    }
}
