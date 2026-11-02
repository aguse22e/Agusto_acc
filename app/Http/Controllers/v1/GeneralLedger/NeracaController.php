<?php

namespace App\Http\Controllers\v1\GeneralLedger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\Jurnal;
use \App\Models\KodeAkun;

class NeracaController extends Controller
{
    private $param;

    public function __construct()
    {
        $this->param['pageTitle'] = 'Laporan Posisi Keuangan (Neraca)';
        $this->param['pageIcon'] = 'fa fa-balance-scale';
        $this->param['parentMenu'] = '#';
        $this->param['current'] = 'Neraca';
    }

    /**
     * PERBAIKAN SAK — modul baru: Laporan Posisi Keuangan (Neraca).
     *
     * Saldo setiap akun dihitung kumulatif dari seluruh jurnal sampai
     * tanggal yang dipilih (neraca adalah potret posisi per tanggal),
     * dengan rumus tanda yang sama seperti Buku Besar / Neraca Saldo:
     *   saldo bertanda = (Debit di posisi kode) + (Kredit di posisi lawan)
     *                  - (Kredit di posisi kode) - (Debit di posisi lawan)
     * Positif berarti saldo normal Debit.
     *
     * Pengelompokan mengikuti awalan kode akun:
     *   1 = Aset, 2 = Liabilitas, 3 = Ekuitas,
     *   4 = Pendapatan, 5 = Beban, 6 = Pajak (nominal, masuk laba berjalan).
     * Laba (Rugi) Tahun Berjalan = total Pendapatan - total Beban & Pajak,
     * disajikan sebagai bagian dari Ekuitas sesuai SAK. Jurnal yang
     * ter-soft-delete (transaksinya di sampah) otomatis tidak terhitung
     * karena memakai model Jurnal (Eloquent).
     */
    private function hitungNeraca($sampai)
    {
        $aset = [];
        $liabilitas = [];
        $ekuitas = [];
        $totalAset = 0;
        $totalLiabilitas = 0;
        $totalEkuitas = 0;
        $totalPendapatan = 0;
        $totalBeban = 0;

        $semuaAkun = KodeAkun::orderBy('kode_akun', 'ASC')->get();
        foreach ($semuaAkun as $akun) {
            $kode = $akun->kode_akun;

            $debitKode = Jurnal::where('kode', $kode)->where('tipe', 'Debit')
                ->where('tanggal', '<=', $sampai)->sum('nominal');
            $kreditKode = Jurnal::where('kode', $kode)->where('tipe', 'Kredit')
                ->where('tanggal', '<=', $sampai)->sum('nominal');
            $debitLawan = Jurnal::where('lawan', $kode)->where('tipe', 'Kredit')
                ->where('tanggal', '<=', $sampai)->sum('nominal');
            $kreditLawan = Jurnal::where('lawan', $kode)->where('tipe', 'Debit')
                ->where('tanggal', '<=', $sampai)->sum('nominal');

            $saldoBertanda = ($debitKode + $debitLawan) - ($kreditKode + $kreditLawan);
            $grup = substr($kode, 0, 1);

            if ($grup === '1') {
                if ($saldoBertanda != 0) {
                    $aset[] = ['akun' => $akun, 'nilai' => $saldoBertanda];
                }
                $totalAset += $saldoBertanda;
            } elseif ($grup === '2') {
                if ($saldoBertanda != 0) {
                    $liabilitas[] = ['akun' => $akun, 'nilai' => -$saldoBertanda];
                }
                $totalLiabilitas += -$saldoBertanda;
            } elseif ($grup === '3') {
                if ($saldoBertanda != 0) {
                    $ekuitas[] = ['akun' => $akun, 'nilai' => -$saldoBertanda];
                }
                $totalEkuitas += -$saldoBertanda;
            } elseif ($grup === '4') {
                $totalPendapatan += -$saldoBertanda;
            } elseif ($grup === '5' || $grup === '6') {
                $totalBeban += $saldoBertanda;
            }
        }

        $labaBerjalan = $totalPendapatan - $totalBeban;
        $totalPasiva = $totalLiabilitas + $totalEkuitas + $labaBerjalan;

        return [
            'aset' => $aset,
            'liabilitas' => $liabilitas,
            'ekuitas' => $ekuitas,
            'totalAset' => $totalAset,
            'totalLiabilitas' => $totalLiabilitas,
            'totalEkuitas' => $totalEkuitas,
            'labaBerjalan' => $labaBerjalan,
            'totalPasiva' => $totalPasiva,
            'selisih' => $totalAset - $totalPasiva,
            'seimbang' => abs($totalAset - $totalPasiva) < 0.5,
        ];
    }

    public function index(Request $request)
    {
        try {
            $tanggalSampai = $request->get('tanggalSampai');
            if (!is_null($tanggalSampai)) {
                $this->param['sampai'] = $tanggalSampai;
                $this->param['neraca'] = $this->hitungNeraca($tanggalSampai);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withStatus($e->getMessage());
        }

        return \view('pages.general-ledger.neraca.index', $this->param);
    }

    public function export(Request $request)
    {
        $tanggalSampai = $request->get('sampai');

        return view('pages.general-ledger.neraca.export', [
            'sampai' => $tanggalSampai,
            'neraca' => $this->hitungNeraca($tanggalSampai),
        ]);
    }
}
