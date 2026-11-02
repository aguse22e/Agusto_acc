<?php

namespace Database\Seeders;

use App\Models\KunciTransaksi;
use Illuminate\Database\Seeder;

class KunciTransaksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jenis = ['Kas','Bank Memorial'];
        for ($i=0; $i < count($jenis); $i++) {
            $addData = new KunciTransaksi;
            $addData->jenis_transaksi = $jenis[$i];
            $addData->tanggal_mulai_kunci = date('Y-m-d');
            $addData->save();
        }
    }
}
