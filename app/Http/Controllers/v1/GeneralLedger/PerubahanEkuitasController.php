<?php

namespace App\Http\Controllers\v1\GeneralLedger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\Jurnal;
use \App\Models\KodeAkun;

class PerubahanEkuitasController extends Controller
{
    private $param;

    public function __construct()
    {
        $this->param['pageTitle'] = 'Laporan Perubahan Ekuitas';
        $this->param['pageIcon'] = 'fa fa-balance-scale';
        $this->param['parentMenu'] = '#';
        $this->param['current'] = 'Perubahan Ekuitas';
    }

    /**
     * PERBAIKAN SAK — modul baru: Laporan Perubahan Ekuitas.
     *
     * Untuk setiap akun ekuitas (3xxx) ditampilkan saldo awal (kumulatif
     * sebelum periode), perubahan selama periode (setoran, prive, koreksi —
     * yaitu semua mutasi jurnal akun itu pada periode), dan saldo akhir.
     * Akun nominal (4xxx, 5xxx, 6xxx) belum pernah ditutup ke ekuitas di
     * aplikasi ini, jadi laba ditampilkan sebagai baris tersendiri:
     * akumulasi laba periode lalu (semua jurnal nominal sebelum periode)
     * dan laba (rugi) periode berjalan. Total ekuitas akhir laporan ini
     * sama dengan total ekuitas + laba berjalan pada Neraca per tanggal
     * "sampai".
     */
    private function jumlahJurnal($kolomAkun, $kode, $tipe, callable $filterTanggal)
    {
        $query = Jurnal::where($kolomAkun, $kode)->where('tipe', $tipe);
        $filterTanggal($query);
        return $query->sum('nominal');
    }

    private function saldoBertanda($kode, callable $filterTanggal)
    {
        $dKode = $this->jumlahJurnal('kode', $kode, 'Debit', $filterTanggal);
        $kKode = $this->jumlahJurnal('kode', $kode, 'Kredit', $filterTanggal);
        $dLawan = $this->jumlahJurnal('lawan', $kode, 'Kredit', $filterTanggal);
        $kLawan = $this->jumlahJurnal('lawan', $kode, 'Debit', $filterTanggal);
        return ($dKode + $dLawan) - ($kKode + $kLawan);
    }

    private function filterSebelum($tanggal)
    {
        return function ($q) use ($tanggal) {
            $q->where('tanggal', '<', $tanggal);
        };
    }

    private function filterSampaiDengan($tanggal)
    {
        return function ($q) use ($tanggal) {
            $q->where('tanggal', '<=', $tanggal);
        };
    }

    private function filterPeriode($dari, $sampai)
    {
        return function ($q) use ($dari, $sampai) {
            $q->whereBetween('tanggal', [$dari, $sampai]);
        };
    }

    private function hitungPerubahan($dari, $sampai)
    {
        $baris = [];
        $totalAwal = 0;
        $totalPerubahan = 0;
        $totalAkhir = 0;

        $akunEkuitas = KodeAkun::where('kode_akun', 'LIKE', '3%')->orderBy('kode_akun', 'ASC')->get();
        foreach ($akunEkuitas as $akun) {
            $kode = $akun->kode_akun;
            $awal = -$this->saldoBertanda($kode, $this->filterSebelum($dari));
            $akhir = -$this->saldoBertanda($kode, $this->filterSampaiDengan($sampai));
            $perubahan = $akhir - $awal;
            if ($awal != 0 || $perubahan != 0 || $akhir != 0) {
                $baris[] = ['akun' => $akun, 'awal' => $awal, 'perubahan' => $perubahan, 'akhir' => $akhir];
            }
            $totalAwal += $awal;
            $totalPerubahan += $perubahan;
            $totalAkhir += $akhir;
        }

        // Laba periode berjalan: mutasi akun nominal khusus periode ini.
        $labaPeriode = 0;
        foreach (KodeAkun::where('kode_akun', 'LIKE', '4%')->pluck('kode_akun') as $kode) {
            $labaPeriode += -$this->saldoBertanda($kode, $this->filterPeriode($dari, $sampai));
        }
        foreach (KodeAkun::where('kode_akun', 'LIKE', '5%')->orWhere('kode_akun', 'LIKE', '6%')->pluck('kode_akun') as $kode) {
            $labaPeriode -= $this->saldoBertanda($kode, $this->filterPeriode($dari, $sampai));
        }

        // Akumulasi laba sampai sebelum periode (belum ditutup ke ekuitas).
        $labaLalu = 0;
        foreach (KodeAkun::where('kode_akun', 'LIKE', '4%')->pluck('kode_akun') as $kode) {
            $labaLalu += -$this->saldoBertanda($kode, $this->filterSebelum($dari));
        }
        foreach (KodeAkun::where('kode_akun', 'LIKE', '5%')->orWhere('kode_akun', 'LIKE', '6%')->pluck('kode_akun') as $kode) {
            $labaLalu -= $this->saldoBertanda($kode, $this->filterSebelum($dari));
        }

        return [
            'baris' => $baris,
            'totalAwal' => $totalAwal,
            'totalPerubahan' => $totalPerubahan,
            'totalAkhir' => $totalAkhir,
            'labaLalu' => $labaLalu,
            'labaPeriode' => $labaPeriode,
            'totalEkuitasAkhir' => $totalAkhir + $labaLalu + $labaPeriode,
        ];
    }

    public function index(Request $request)
    {
        try {
            $tanggalDari = $request->get('tanggalDari');
            $tanggalSampai = $request->get('tanggalSampai');
            if (!is_null($tanggalDari) && !is_null($tanggalSampai)) {
                if ($tanggalDari > $tanggalSampai) {
                    return back()->withError('Tanggal tidak valid.');
                }
                $this->param['dari'] = $tanggalDari;
                $this->param['sampai'] = $tanggalSampai;
                $this->param['perubahan'] = $this->hitungPerubahan($tanggalDari, $tanggalSampai);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withStatus($e->getMessage());
        }

        return \view('pages.general-ledger.perubahan-ekuitas.index', $this->param);
    }

    public function export(Request $request)
    {
        $tanggalDari = $request->get('dari');
        $tanggalSampai = $request->get('sampai');

        return view('pages.general-ledger.perubahan-ekuitas.export', [
            'dari' => $tanggalDari,
            'sampai' => $tanggalSampai,
            'perubahan' => $this->hitungPerubahan($tanggalDari, $tanggalSampai),
        ]);
    }
}
