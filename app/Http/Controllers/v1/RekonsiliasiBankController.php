<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use App\Models\KodeAkun;
use App\Models\RekonsiliasiBank;
use App\Models\TransaksiBank;
use App\Models\UserActivity;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RekonsiliasiBankController extends Controller
{
    private $param;

    public function __construct()
    {
        $this->param['pageTitle'] = 'Rekonsiliasi Bank';
        $this->param['pageIcon'] = 'ti-check-box';
        $this->param['parentMenu'] = 'Transaksi Bank';
        $this->param['current'] = 'Rekonsiliasi Bank';
    }

    /**
     * PERBAIKAN SAK — modul baru: Rekonsiliasi Bank.
     *
     * Membandingkan saldo bank menurut pembukuan (dihitung dari jurnal sampai
     * akhir periode terpilih) dengan saldo menurut rekening koran. Selisih
     * waktu dijelaskan oleh transaksi yang belum kliring: setoran dalam
     * perjalanan (Masuk belum kliring) menambah, cek atau giro beredar
     * (Keluar belum kliring) mengurangi. Rumusnya:
     *   saldo koran + belum kliring Masuk - belum kliring Keluar
     *   harus sama dengan saldo buku; selisih selain nol berarti ada mutasi
     *   pada rekening koran yang belum dicatat, atau sebaliknya.
     */
    public function index(Request $request)
    {
        $this->param['btnText'] = 'Kembali';
        $this->param['btnLink'] = route('bank-transaksi.index');
        try {
            $this->param['kodeAkun'] = KodeAkun::where('jenis_akun', 'Bank')->orderBy('kode_akun')->get();
            $this->param['akunTerpilih'] = $request->get('akun');
            $this->param['periode'] = $request->get('periode');
            $this->param['hasil'] = null;

            if ($this->param['akunTerpilih'] && $this->param['periode']) {
                $akun = $this->param['akunTerpilih'];
                $awalPeriode = $this->param['periode'] . '-01';
                $akhirPeriode = date('Y-m-t', strtotime($awalPeriode));

                // Saldo buku dari jurnal (akun sebagai akun utama maupun lawan)
                $saldoBuku = 0;
                $jurnal = Jurnal::where('tanggal', '<=', $akhirPeriode)
                    ->where(function ($q) use ($akun) {
                        $q->where('kode', $akun)->orWhere('lawan', $akun);
                    })->get();
                foreach ($jurnal as $baris) {
                    if ($baris->kode == $akun) {
                        $saldoBuku += $baris->tipe == 'Debit' ? $baris->nominal : -$baris->nominal;
                    } else {
                        $saldoBuku += $baris->tipe == 'Debit' ? -$baris->nominal : $baris->nominal;
                    }
                }

                $transaksi = TransaksiBank::where('akun_kode', $akun)
                    ->where('tanggal', '<=', $akhirPeriode)
                    ->orderBy('tanggal')->orderBy('created_at')
                    ->get();

                $belumMasuk = $transaksi->where('tipe', 'Masuk')->where('status_kliring', 'Belum')->sum('total');
                $belumKeluar = $transaksi->where('tipe', 'Keluar')->where('status_kliring', 'Belum')->sum('total');

                $rekonsiliasi = RekonsiliasiBank::where('akun_kode', $akun)->where('periode', $awalPeriode)->first();
                $saldoKoran = $rekonsiliasi ? (float) $rekonsiliasi->saldo_koran : 0;
                $saldoDisesuaikan = $saldoKoran + $belumMasuk - $belumKeluar;

                $this->param['hasil'] = [
                    'akhirPeriode' => $akhirPeriode,
                    'saldoBuku' => $saldoBuku,
                    'saldoKoran' => $saldoKoran,
                    'belumMasuk' => $belumMasuk,
                    'belumKeluar' => $belumKeluar,
                    'saldoDisesuaikan' => $saldoDisesuaikan,
                    'selisih' => $saldoBuku - $saldoDisesuaikan,
                    'transaksi' => $transaksi,
                ];
            }

            return view('pages.rekonsiliasi-bank.index', $this->param);
        } catch (QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan.' . $e->getMessage());
        }
    }

    public function simpanSaldoKoran(Request $request)
    {
        $request->validate([
            'akun' => 'required',
            'periode' => 'required',
            'saldo_koran' => 'required|numeric',
        ]);
        try {
            $akun = KodeAkun::where('jenis_akun', 'Bank')->where('kode_akun', $request->akun)->first();
            if (!$akun) {
                return redirect()->back()->withError('Akun terpilih bukan akun berjenis Bank.');
            }
            RekonsiliasiBank::updateOrCreate(
                ['akun_kode' => $request->akun, 'periode' => $request->periode . '-01'],
                ['saldo_koran' => $request->saldo_koran]
            );

            $aktivitas = new UserActivity;
            $aktivitas->id_user = Auth::user()->id;
            $aktivitas->jenis_transaksi = 'Bank';
            $aktivitas->tipe = 'Update';
            $aktivitas->keterangan = 'Menyimpan saldo rekening koran akun ' . $request->akun . ' periode ' . $request->periode . ' sebesar ' . $request->saldo_koran . '.';
            $aktivitas->save();

            return redirect()->route('rekonsiliasi-bank.index', ['akun' => $request->akun, 'periode' => $request->periode])
                ->withStatus('Saldo rekening koran berhasil disimpan.');
        } catch (QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan.' . $e->getMessage());
        }
    }

    public function simpanKliring(Request $request)
    {
        $request->validate([
            'akun' => 'required',
            'periode' => 'required',
            'semua' => 'required|array',
        ]);
        try {
            $sudahKliring = (array) $request->input('kliring', []);
            foreach ((array) $request->input('semua') as $kode) {
                $kliring = in_array($kode, $sudahKliring);
                TransaksiBank::where('kode_transaksi_bank', $kode)->update([
                    'status_kliring' => $kliring ? 'Kliring' : 'Belum',
                    'tanggal_kliring' => $kliring ? date('Y-m-d') : null,
                ]);
            }

            $aktivitas = new UserActivity;
            $aktivitas->id_user = Auth::user()->id;
            $aktivitas->jenis_transaksi = 'Bank';
            $aktivitas->tipe = 'Update';
            $aktivitas->keterangan = 'Memperbarui status kliring rekonsiliasi bank akun ' . $request->akun . ' periode ' . $request->periode . '.';
            $aktivitas->save();

            return redirect()->route('rekonsiliasi-bank.index', ['akun' => $request->akun, 'periode' => $request->periode])
                ->withStatus('Status kliring berhasil disimpan.');
        } catch (QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan.' . $e->getMessage());
        }
    }
}
