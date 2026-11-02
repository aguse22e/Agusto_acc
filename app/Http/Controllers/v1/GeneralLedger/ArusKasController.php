<?php

namespace App\Http\Controllers\v1\GeneralLedger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\Jurnal;
use \App\Models\KodeAkun;

class ArusKasController extends Controller
{
    private $param;

    public function __construct()
    {
        $this->param['pageTitle'] = 'Laporan Arus Kas';
        $this->param['pageIcon'] = 'fa fa-balance-scale';
        $this->param['parentMenu'] = '#';
        $this->param['current'] = 'Arus Kas';
    }

    /**
     * PERBAIKAN SAK — modul baru: Laporan Arus Kas (metode langsung).
     *
     * Akun kas & setara kas dikenali dengan konvensi aplikasi sendiri:
     * akun yang namanya diawali "Kas" atau "Bank" (lihat
     * TransaksiKasController::show). Setiap baris jurnal pada periode yang
     * salah satu sisinya akun kas (dan sisi lainnya bukan) adalah arus kas
     * nyata; transfer antar akun kas diabaikan karena tidak mengubah total
     * kas. Besaran arus dihitung dari sisi kasnya:
     *   - akun kas di posisi kode  : Debit = masuk, Kredit = keluar
     *   - akun kas di posisi lawan : berlawanan dengan tipe baris
     * Klasifikasi aktivitas dari akun lawannya:
     *   - Operasi   : pendapatan/beban/pajak (4xxx, 5xxx, 6xxx) dan
     *                 liabilitas (2xxx), kecuali nama akun mengandung
     *                 "Bank" (pinjaman bank = Pendanaan)
     *   - Investasi : aset non-kas (1xxx di luar kas/bank)
     *   - Pendanaan : ekuitas (3xxx) dan pinjaman bank (2xxx "Bank")
     * Jurnal ter-soft-delete otomatis tidak terhitung (model Eloquent).
     */
    private function akunKas()
    {
        return KodeAkun::where('nama', 'LIKE', 'Kas%')
            ->orWhere('nama', 'LIKE', 'Bank%')
            ->pluck('kode_akun')->toArray();
    }

    private function saldoKas($daftarKas, $kondisi, $tanggal)
    {
        $total = 0;
        foreach ($daftarKas as $kode) {
            $dKode = Jurnal::where('kode', $kode)->where('tipe', 'Debit')->where('tanggal', $kondisi, $tanggal)->sum('nominal');
            $kKode = Jurnal::where('kode', $kode)->where('tipe', 'Kredit')->where('tanggal', $kondisi, $tanggal)->sum('nominal');
            $dLawan = Jurnal::where('lawan', $kode)->where('tipe', 'Kredit')->where('tanggal', $kondisi, $tanggal)->sum('nominal');
            $kLawan = Jurnal::where('lawan', $kode)->where('tipe', 'Debit')->where('tanggal', $kondisi, $tanggal)->sum('nominal');
            $total += ($dKode + $dLawan) - ($kKode + $kLawan);
        }
        return $total;
    }

    private function klasifikasi($akunLawan)
    {
        $grup = substr($akunLawan->kode_akun, 0, 1);
        if ($grup === '1') {
            return 'investasi';
        }
        if ($grup === '3') {
            return 'pendanaan';
        }
        if ($grup === '2' && stripos($akunLawan->nama, 'bank') !== false) {
            return 'pendanaan';
        }
        return 'operasi';
    }

    private function hitungArusKas($dari, $sampai)
    {
        $daftarKas = $this->akunKas();
        $kasAwal = $this->saldoKas($daftarKas, '<', $dari);
        $kasAkhir = $this->saldoKas($daftarKas, '<=', $sampai);

        $baris = Jurnal::whereBetween('tanggal', [$dari, $sampai])
            ->where(function ($q) use ($daftarKas) {
                $q->where(function ($q2) use ($daftarKas) {
                    $q2->whereIn('kode', $daftarKas)->whereNotIn('lawan', $daftarKas);
                })->orWhere(function ($q2) use ($daftarKas) {
                    $q2->whereIn('lawan', $daftarKas)->whereNotIn('kode', $daftarKas);
                });
            })
            ->orderBy('tanggal', 'ASC')->get();

        $kelompok = ['operasi' => [], 'investasi' => [], 'pendanaan' => []];
        $subtotal = ['operasi' => 0, 'investasi' => 0, 'pendanaan' => 0];
        $akunByKode = KodeAkun::all()->keyBy('kode_akun');

        foreach ($baris as $j) {
            if (in_array($j->kode, $daftarKas)) {
                $delta = $j->tipe === 'Debit' ? $j->nominal : -$j->nominal;
                $kodeLawan = $j->lawan;
            } else {
                $delta = $j->tipe === 'Kredit' ? $j->nominal : -$j->nominal;
                $kodeLawan = $j->kode;
            }
            $akunLawan = $akunByKode->get($kodeLawan);
            if (!$akunLawan) {
                continue;
            }
            $aktivitas = $this->klasifikasi($akunLawan);
            if (!isset($kelompok[$aktivitas][$kodeLawan])) {
                $kelompok[$aktivitas][$kodeLawan] = ['akun' => $akunLawan, 'nilai' => 0];
            }
            $kelompok[$aktivitas][$kodeLawan]['nilai'] += $delta;
            $subtotal[$aktivitas] += $delta;
        }

        $kenaikanBersih = $subtotal['operasi'] + $subtotal['investasi'] + $subtotal['pendanaan'];

        return [
            'operasi' => $kelompok['operasi'],
            'investasi' => $kelompok['investasi'],
            'pendanaan' => $kelompok['pendanaan'],
            'totalOperasi' => $subtotal['operasi'],
            'totalInvestasi' => $subtotal['investasi'],
            'totalPendanaan' => $subtotal['pendanaan'],
            'kenaikanBersih' => $kenaikanBersih,
            'kasAwal' => $kasAwal,
            'kasAkhir' => $kasAkhir,
            'cocok' => abs($kenaikanBersih - ($kasAkhir - $kasAwal)) < 0.5,
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
                $this->param['arusKas'] = $this->hitungArusKas($tanggalDari, $tanggalSampai);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withStatus($e->getMessage());
        }

        return \view('pages.general-ledger.arus-kas.index', $this->param);
    }

    public function export(Request $request)
    {
        $tanggalDari = $request->get('dari');
        $tanggalSampai = $request->get('sampai');

        return view('pages.general-ledger.arus-kas.export', [
            'dari' => $tanggalDari,
            'sampai' => $tanggalSampai,
            'arusKas' => $this->hitungArusKas($tanggalDari, $tanggalSampai),
        ]);
    }
}
