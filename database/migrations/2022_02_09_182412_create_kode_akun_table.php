<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKodeAkunTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kode_akun', function (Blueprint $table) {
            $table->string('kode_akun',15)->primary()->unique();
            $table->string('induk_kode');
            $table->foreign('induk_kode')->references('kode_induk')->on('kode_induk');
            $table->string('nama',40);
            $table->decimal('saldo_awal',13,2);
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
        Schema::dropIfExists('kode_akun');
    }
}
