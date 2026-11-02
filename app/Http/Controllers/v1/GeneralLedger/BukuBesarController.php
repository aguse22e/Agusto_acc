<?php

namespace App\Http\Controllers\v1\GeneralLedger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use \App\Models\Jurnal;
use \App\Models\KodeAkun;
use \App\Exports\BukuBesarExport;

class BukuBesarController extends Controller
{
    private $param;

    public function __construct()
    {
        $this->param['pageTitle'] = 'Buku Besar';
        $this->param['pageIcon'] = 'feather icon-book';
        $this->param['parentMenu'] = '#';
        $this->param['current'] = 'Buku Besar';
    }

    public function index(Request $request)
    {
        try {
            $this->param['allAkun'] = KodeAkun::orderBy('kode_akun', 'ASC')->get();

            $kodeAkun = $request->get('kodeAkun');
            // return $kodeAkun;
            // $kodeRekeningSampai = $request->get('kodeRekeningSampai');
            $tanggalDari = $request->get('tanggalDari');
            $tanggalSampai = $request->get('tanggalSampai');

            if ($tanggalDari > $tanggalSampai) {
                return back()->withError('Tanggal tidak valid.');
            }

            $isAll = false;
            if (!is_null($kodeAkun) && !is_null($tanggalDari) && !is_null($tanggalSampai) ) {

                if (in_array('all',$kodeAkun)) {
                    $kodeAkun = KodeAkun::pluck('kode_akun');
                    $isAll = true;
                }
                $this->param['kodeAkun'] = KodeAkun::with('kodeInduk')->whereIn('kode_akun', $kodeAkun)->orderBy('kode_akun', 'ASC')->get();
                $this->param['selectedAkun'] = $kodeAkun;
                $this->param['dari'] = $tanggalDari;
                $this->param['sampai'] = $tanggalSampai;
            }
            $this->param['isAll'] = $isAll;
            // return $kodeAkun;
            // return implode(',', $kodeAkun);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage());
        }

        return \view('pages.general-ledger.buku-besar.index', $this->param);
    }

    public function export(Request $request)
    {
        // return $kodeAkun;
        // $kodeRekeningSampai = $request->get('kodeRekeningSampai');

        $kodeAkun = $request->get('kodeAkun');
        $tanggalDari = $request->get('dari');
        $tanggalSampai = $request->get('sampai');
        $isAll = false;
        if (!is_null($kodeAkun) && !is_null($tanggalDari) && !is_null($tanggalSampai) ) {

            if (!is_array($kodeAkun) && $kodeAkun == 'all') {
                $kodeAkun = KodeAkun::pluck('kode_akun');
                $isAll = true;
            }
            else{
                $kodeAkun = explode(',', $kodeAkun);
            }

            $this->param['kodeAkun'] = KodeAkun::with('kodeInduk')->whereIn('kode_akun', $kodeAkun)->orderBy('kode_akun', 'ASC')->get();

            $this->param['dari'] = $tanggalDari;
            $this->param['sampai'] = $tanggalSampai;
        }

        return view('pages.general-ledger.buku-besar.export', [
            'kodeAkun' => $this->param['kodeAkun'],
            'dari' => $this->param['dari'],
            'sampai' => $this->param['sampai'],
        ]);

    }
}
