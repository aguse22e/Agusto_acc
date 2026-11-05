<?php

namespace App\Http\Traits;

use App\Models\Jurnal;
use App\Models\Memorial;
use App\Models\MemorialDetail;
use App\Models\UserActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait JurnalKoreksi
{
    /**
     * PERBAIKAN SAK — Jurnal Koreksi sebagai jalur resmi pembetulan.
     *
     * Membuat Memorial baru bertanggal hari ini yang membalik seluruh baris
     * jurnal transaksi asal (akun utama dan akun lawan ditukar, sisi mengikuti
     * tipe asal) sehingga saldo transaksi asal menjadi nol tanpa mengubah
     * jurnal asalnya. Jejak audit utuh: transaksi asal tetap terbaca, dan
     * memorial koreksi menunjuk balik lewat kolom koreksi_dari. Koreksi atas
     * transaksi yang sama hanya boleh dibuat satu kali.
     *
     * Membutuhkan SequenceTrait pada controller pemakai (untuk
     * generateNomorTransaksi). Mengembalikan [bool $berhasil, string $pesan,
     * string|null $kodeMemorialBaru].
     */
    protected function buatJurnalKoreksi($jenisAsal, $kodeAsal)
    {
        $jurnalAsal = Jurnal::where('jenis_transaksi', $jenisAsal)
            ->where('kode_transaksi', $kodeAsal)
            ->get();

        if ($jurnalAsal->isEmpty()) {
            return [false, 'Jurnal transaksi ' . $kodeAsal . ' tidak ditemukan, jurnal koreksi tidak dibuat.', null];
        }

        if (Memorial::where('koreksi_dari', $kodeAsal)->exists()) {
            return [false, 'Transaksi ' . $kodeAsal . ' sudah pernah dikoreksi dengan jurnal koreksi. Jurnal asal dibiarkan utuh; bila koreksinya sendiri keliru, koreksi memorial koreksinya.', null];
        }

        DB::beginTransaction();
        try {
            $tanggal = date('Y-m-d');
            // Sisi akun utama pada memorial: 'Masuk' berarti Debit. Baris
            // koreksi harus memberi akun lawan asal sisi yang sama dengan
            // sisi akun utama asal, agar posisinya terbalik persis.
            $tipeAsal = $jurnalAsal->first()->tipe;
            $tipeMemorial = $tipeAsal == 'Debit' ? 'Masuk' : 'Keluar';
            $kodePrefix = $tipeMemorial == 'Masuk' ? 'BMM' : 'BMK';
            $kodeMemorial = $this->generateNomorTransaksi($kodePrefix, date('Y', strtotime($tanggal)), date('m', strtotime($tanggal)), null);

            $total = 0;
            foreach ($jurnalAsal as $baris) {
                $total += $baris->nominal;
            }

            $memorial = new Memorial;
            $memorial->kode_memorial = $kodeMemorial;
            $memorial->tanggal = $tanggal;
            $memorial->tipe = $tipeMemorial;
            $memorial->total = $total;
            $memorial->koreksi_dari = $kodeAsal;
            $memorial->save();

            foreach ($jurnalAsal as $baris) {
                $keterangan = 'Koreksi atas ' . $kodeAsal . ': ' . $baris->keterangan;

                $detail = new MemorialDetail;
                $detail->kode_memorial = $kodeMemorial;
                $detail->keterangan = $keterangan;
                $detail->kode = $baris->lawan;
                $detail->lawan = $baris->kode;
                $detail->subtotal = $baris->nominal;
                $detail->save();

                $jurnal = new Jurnal;
                $jurnal->tanggal = $tanggal;
                $jurnal->jenis_transaksi = 'Memorial';
                $jurnal->kode_transaksi = $kodeMemorial;
                $jurnal->keterangan = $keterangan;
                $jurnal->kode = $baris->lawan;
                $jurnal->lawan = $baris->kode;
                $jurnal->tipe = $baris->tipe;
                $jurnal->nominal = $baris->nominal;
                $jurnal->id_detail = $detail->id;
                $jurnal->save();
            }

            $aktivitas = new UserActivity;
            $aktivitas->id_user = Auth::user()->id;
            $aktivitas->jenis_transaksi = 'Memorial';
            $aktivitas->tipe = 'Insert';
            $aktivitas->keterangan = 'Membuat Jurnal Koreksi ' . $kodeMemorial . ' atas transaksi ' . $jenisAsal . ' ' . $kodeAsal . ' dengan total ' . $total . '.';
            $aktivitas->save();

            DB::commit();
            return [true, 'Jurnal Koreksi ' . $kodeMemorial . ' berhasil dibuat atas transaksi ' . $kodeAsal . '. Jurnal asal tidak diubah.', $kodeMemorial];
        } catch (\Exception $e) {
            DB::rollBack();
            return [false, 'Jurnal koreksi gagal dibuat: ' . $e->getMessage(), null];
        }
    }
}
