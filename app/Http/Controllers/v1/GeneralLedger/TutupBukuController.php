<?php

namespace App\Http\Controllers\v1\GeneralLedger;

use App\Http\Controllers\Controller;
use App\Http\Traits\SequenceTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use \App\Models\Jurnal;
use \App\Models\KodeAkun;
use \App\Models\Memorial;
use \App\Models\MemorialDetail;
use \App\Models\UserActivity;

class TutupBukuController extends Controller
{
    use SequenceTrait;

    private $param;

    public function __construct()
    {
        $this->param['pageTitle'] = 'Tutup Buku (Jurnal Penutup)';
        $this->param['pageIcon'] = 'fa fa-balance-scale';
        $this->param['parentMenu'] = '#';
        $this->param['current'] = 'Tutup Buku';
    }

    /**
     * PERBAIKAN SAK — modul baru: Tutup Buku / jurnal penutup.
     *
     * Akun nominal (4xxx pendapatan, 5xxx beban, 6xxx pajak) bersifat
     * sementara: saldonya harus dinolkan ke akun Laba Ditahan (ekuitas,
     * 3xxx) pada akhir periode. Modul ini menghitung saldo kumulatif akun
     * nominal sampai tanggal tutup, lalu membuat bukti memorial penutup
     * persis seperti MemorialController::store:
     *   - saldo Kredit (umumnya pendapatan) ditutup lewat memorial Masuk
     *     (BMM, jurnal Debit): akun nominal di-Debit, Laba Ditahan
     *     di-Kredit (posisi lawan).
     *   - saldo Debit (umumnya beban) ditutup lewat memorial Keluar
     *     (BMK, jurnal Kredit): akun nominal di-Kredit, Laba Ditahan
     *     di-Debit (posisi lawan).
     * Pengelompokan mengikuti sisi saldo yang sebenarnya, jadi akun
     * pendapatan bersaldo Debit atau beban bersaldo Kredit tetap ditutup
     * dengan benar. Setelah proses, saldo akun nominal sampai tanggal
     * tutup menjadi nol dan laba kumulatif berpindah ke Laba Ditahan,
     * sehingga Neraca dan Perubahan Ekuitas tetap seimbang. Menjalankan
     * tutup buku dua kali untuk tanggal yang sama aman: saldo yang sudah
     * nol tidak membuat voucher baru.
     */
    private function saldoBertandaSampai($kode, $tanggal)
    {
        $dKode = Jurnal::where('kode', $kode)->where('tipe', 'Debit')->where('tanggal', '<=', $tanggal)->sum('nominal');
        $kKode = Jurnal::where('kode', $kode)->where('tipe', 'Kredit')->where('tanggal', '<=', $tanggal)->sum('nominal');
        $dLawan = Jurnal::where('lawan', $kode)->where('tipe', 'Kredit')->where('tanggal', '<=', $tanggal)->sum('nominal');
        $kLawan = Jurnal::where('lawan', $kode)->where('tipe', 'Debit')->where('tanggal', '<=', $tanggal)->sum('nominal');
        return ($dKode + $dLawan) - ($kKode + $kLawan);
    }

    private function barisPenutup($tanggal)
    {
        $barisDebit = [];
        $barisKredit = [];
        $akunNominal = KodeAkun::where('kode_akun', 'LIKE', '4%')
            ->orWhere('kode_akun', 'LIKE', '5%')
            ->orWhere('kode_akun', 'LIKE', '6%')
            ->orderBy('kode_akun', 'ASC')->get();
        foreach ($akunNominal as $akun) {
            $saldo = $this->saldoBertandaSampai($akun->kode_akun, $tanggal);
            if ($saldo < 0) {
                $barisDebit[] = ['akun' => $akun, 'saldo' => $saldo, 'jumlah' => -$saldo];
            } elseif ($saldo > 0) {
                $barisKredit[] = ['akun' => $akun, 'saldo' => $saldo, 'jumlah' => $saldo];
            }
        }
        return ['debit' => $barisDebit, 'kredit' => $barisKredit];
    }

    public function index(Request $request)
    {
        try {
            $this->param['akunEkuitas'] = KodeAkun::where('kode_akun', 'LIKE', '3%')->orderBy('kode_akun', 'ASC')->get();
            $tanggal = $request->get('tanggal');
            $akunLabaDitahan = $request->get('akunLabaDitahan');
            if (!is_null($tanggal) && !is_null($akunLabaDitahan)) {
                $this->param['tanggal'] = $tanggal;
                $this->param['akunLabaDitahan'] = KodeAkun::find($akunLabaDitahan);
                $this->param['penutup'] = $this->barisPenutup($tanggal);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withStatus($e->getMessage());
        }

        return \view('pages.general-ledger.tutup-buku.index', $this->param);
    }

    private function buatVoucherPenutup($tanggal, $tipe, $baris, $akunLabaDitahan)
    {
        $total = 0;
        foreach ($baris as $row) {
            $total += $row['jumlah'];
        }

        $kode = $tipe == 'Masuk' ? 'BMM' : 'BMK';
        $tahun = date('Y', strtotime($tanggal));
        $bulan = date('m', strtotime($tanggal));
        $kodeMemorial = $this->generateNomorTransaksi($kode, $tahun, $bulan, null);

        $addMemorial = new Memorial;
        $addMemorial->kode_memorial = $kodeMemorial;
        $addMemorial->tanggal = $tanggal;
        $addMemorial->tipe = $tipe;
        $addMemorial->total = $total;
        $addMemorial->save();

        $addUserActivity = new UserActivity;
        $addUserActivity->id_user = Auth::user()->id;
        $addUserActivity->jenis_transaksi = 'Memorial';
        $addUserActivity->tipe = 'Insert';
        $addUserActivity->keterangan = 'Tutup Buku: berhasil menambahkan Memorial penutup dengan kode ' . $kodeMemorial . ' dengan total ' . $total . '.';
        $addUserActivity->save();

        foreach ($baris as $row) {
            $keterangan = 'Jurnal Penutup ' . date('d-m-Y', strtotime($tanggal)) . ' — tutup akun ' . $row['akun']->kode_akun . ' ' . $row['akun']->nama . ' ke ' . $akunLabaDitahan->kode_akun . ' ' . $akunLabaDitahan->nama;

            $addDetail = new MemorialDetail;
            $addDetail->kode_memorial = $kodeMemorial;
            $addDetail->keterangan = $keterangan;
            $addDetail->kode = $row['akun']->kode_akun;
            $addDetail->lawan = $akunLabaDitahan->kode_akun;
            $addDetail->subtotal = $row['jumlah'];
            $addDetail->save();

            $addJurnal = new Jurnal;
            $addJurnal->tanggal = $tanggal;
            $addJurnal->jenis_transaksi = 'Memorial';
            $addJurnal->kode_transaksi = $kodeMemorial;
            $addJurnal->keterangan = $keterangan;
            $addJurnal->kode = $row['akun']->kode_akun;
            $addJurnal->lawan = $akunLabaDitahan->kode_akun;
            $addJurnal->tipe = $tipe == 'Masuk' ? 'Debit' : 'Kredit';
            $addJurnal->nominal = $row['jumlah'];
            $addJurnal->id_detail = $addDetail->id;
            $addJurnal->save();
        }

        return $kodeMemorial;
    }

    public function proses(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'akunLabaDitahan' => 'required',
        ]);

        $tanggal = $request->get('tanggal');
        $akunLabaDitahan = KodeAkun::find($request->get('akunLabaDitahan'));
        if (!$akunLabaDitahan || substr($akunLabaDitahan->kode_akun, 0, 1) !== '3') {
            return back()->withError('Akun Laba Ditahan harus akun ekuitas (3xxx).');
        }

        $penutup = $this->barisPenutup($tanggal);
        if (count($penutup['debit']) == 0 && count($penutup['kredit']) == 0) {
            return back()->withError('Tidak ada saldo akun nominal yang perlu ditutup sampai tanggal tersebut (sudah nol / sudah pernah ditutup).');
        }

        DB::beginTransaction();
        try {
            $voucher = [];
            if (count($penutup['debit']) > 0) {
                $voucher[] = $this->buatVoucherPenutup($tanggal, 'Masuk', $penutup['debit'], $akunLabaDitahan);
            }
            if (count($penutup['kredit']) > 0) {
                $voucher[] = $this->buatVoucherPenutup($tanggal, 'Keluar', $penutup['kredit'], $akunLabaDitahan);
            }
            DB::commit();
            return redirect('general-ledger/tutup-buku')->withStatus('Tutup buku berhasil. Voucher penutup: ' . implode(', ', $voucher) . '. Saldo akun nominal sampai ' . $tanggal . ' kini nol dan laba berpindah ke ' . $akunLabaDitahan->nama . '.');
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return back()->withError('Terjadi kesalahan. ' . $e->getMessage());
        }
    }
}
