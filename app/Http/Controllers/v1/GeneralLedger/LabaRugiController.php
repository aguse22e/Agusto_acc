<?php

namespace App\Http\Controllers\v1\GeneralLedger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use \App\Models\Jurnal;
use \App\Models\KodeAkun;

class LabaRugiController extends Controller
{
    private $param;

    public function __construct()
    {
        $this->param['pageTitle'] = 'Laba Rugi';
        $this->param['pageIcon'] = 'fa fa-chart-line';
        $this->param['parentMenu'] = '#';
        $this->param['current'] = 'Laba Rugi';
    }

    public function index(Request $request)
    {
        try {
            $this->param['allBulan'] = array(
                [
                    'bulan' => '01',
                    'nama' => 'Januari'
                ],
                [
                    'bulan' => '02',
                    'nama' => 'Februari'
                ],
                [
                    'bulan' => '03',
                    'nama' => 'Maret'
                ],
                [
                    'bulan' => '04',
                    'nama' => 'April'
                ],
                [
                    'bulan' => '05',
                    'nama' => 'Mei'
                ],
                [
                    'bulan' => '06',
                    'nama' => 'Juni'
                ],
                [
                    'bulan' => '07',
                    'nama' => 'Juli'
                ],
                [
                    'bulan' => '08',
                    'nama' => 'Agustus'
                ],
                [
                    'bulan' => '09',
                    'nama' => 'September'
                ],
                [
                    'bulan' => '10',
                    'nama' => 'Oktober'
                ],
                [
                    'bulan' => '11',
                    'nama' => 'November'
                ],
                [
                    'bulan' => '12',
                    'nama' => 'Desember'
                ],
            );

            $month = $request->get('month');
            $year = $request->get('year');
            if ($month > date('m') && $year >= date('Y')) {
                return back()->withError('Periode tidak valid.');
            }
            if (!is_null($month) && !is_null($year)) {

                $this->param['rekeningPenjualan'] = KodeAkun::with('kodeInduk')->where('kode_akun', 'LIKE', '4%')->orderBy('kode_akun', 'ASC')->get();

                // $this->param['hpp'] = \DB::table('rekap_hpp_bulanan')->select('nominal')->where('bulan', $month)->where('tahun', $year)->get();

                $this->param['rekeningBeban'] = KodeAkun::with('kodeInduk')->where('kode_akun', 'LIKE', '5%')->orderBy('kode_akun', 'ASC')->get();
                
                $this->param['rekeningPajak'] = KodeAkun::with('kodeInduk')->where('kode_akun', 'LIKE', '6%')->orderBy('kode_akun', 'ASC')->get();

                // if (count($this->param['hpp']) == 0) {
                //     return redirect('/general-ledger/laba-rugi')->withStatus('Hpp Bulan Tersebut Belum Diinput.');
                // }
                // else{
                //     $this->param['hpp'] =  $this->param['hpp'][0]->nominal;
                // }

                $this->param['month'] = $month;
                $this->param['year'] = $year;
                
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withStatus($e->getMessage());
        }

        return \view('pages.general-ledger.laba-rugi.index', $this->param);
    }

    public function export(Request $request)
    {
        $month = $request->get('month');
            $year = $request->get('year');

            if (!is_null($month) && !is_null($year)) {

                $this->param['rekeningPenjualan'] = KodeAkun::with('kodeInduk')->where('kode_akun', 'LIKE', '4%')->orderBy('kode_akun', 'ASC')->get();

                // $this->param['hpp'] = \DB::table('rekap_hpp_bulanan')->select('nominal')->where('bulan', $month)->where('tahun', $year)->get();

                $this->param['rekeningBeban'] = KodeAkun::with('kodeInduk')->where('kode_akun', 'LIKE', '5%')->orderBy('kode_akun', 'ASC')->get();
                
                $this->param['rekeningPajak'] = KodeAkun::with('kodeInduk')->where('kode_akun', 'LIKE', '6%')->orderBy('kode_akun', 'ASC')->get();

                // if (count($this->param['hpp']) == 0) {
                //     return redirect('/general-ledger/laba-rugi')->withStatus('Hpp Bulan Tersebut Belum Diinput.');
                // }
                // else{
                //     $this->param['hpp'] =  $this->param['hpp'][0]->nominal;
                // }

                $this->param['month'] = $month;
                $this->param['year'] = $year;
                
            }

        return view('pages.general-ledger.laba-rugi.export', $this->param);

    }
}
