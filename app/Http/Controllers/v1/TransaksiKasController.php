<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\KodeAkun;
use App\Models\TransaksiKas;
use App\Models\TransaksiKasDetail;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\SequenceTrait;
use App\Http\Traits\PeriodeGuard;
use App\Http\Traits\JurnalKoreksi;
use App\Models\UserActivity;
use Illuminate\Support\Facades\Auth;

class TransaksiKasController extends Controller
{
    private $param;

    use SequenceTrait, PeriodeGuard, JurnalKoreksi;

    public function __construct()
    {
        $this->param['pageTitle'] = 'Transaksi Kas / List Transaksi Kas';
        $this->param['pageIcon'] = 'ti-wallet';
        $this->param['parentMenu'] = 'Transaksi Kas';
        $this->param['current'] = 'Transaksi Kas';
    }
    public function index(Request $request)
    {
        // $data =  TransaksiKas::orderBy('tanggal','DESC')->get();
        $this->param['btnText'] = 'Tambah Data';
        $this->param['btnLink'] = route('kas-transaksi.create');
        try {
            $keyword = $request->get('keyword');
            $getTransaksiKas = TransaksiKas::orderBy('tanggal', 'DESC')->orderBy('created_at', 'DESC');

            if ($keyword) {
                $getTransaksiKas->where('kode_transaksi_kas', 'LIKE', "%{$keyword}%")->orWhere('tipe', 'LIKE', "%{$keyword}%")->orWhere('akun_kode', 'LIKE', "%{$keyword}%");
            }

            $this->param['transaksi_kas'] = $getTransaksiKas->paginate(10);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        } catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }

        return view('pages.transaksi-kas.index', $this->param);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->param['btnText'] = 'Lihat Data';
        $this->param['btnLink'] = route('kas-transaksi.index');
        $this->param['kodeAkun'] = KodeAkun::select('kode_akun.kode_akun', 'kode_akun.nama')
                                            ->join('kode_induk', 'kode_akun.induk_kode', 'kode_induk.kode_induk')
                                            ->where('kode_akun.jenis_akun', 'Kas')
                                            ->get();
        $this->param['kode_lawan'] = KodeAkun::select('kode_akun.kode_akun', 'kode_akun.nama')
                                            ->join('kode_induk', 'kode_akun.induk_kode', 'kode_induk.kode_induk')
                                            ->get();
        return view('pages.transaksi-kas.create', $this->param);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'tipe' => 'required|not_in:0',
            'kode_akun' => 'required|not_in:0',
            'kode_lawan.*' => 'required|not_in:0',
            'subtotal.*' => 'required|numeric|gt:0',
            'keterangan.*' => 'required',
        ],[
            'required' => ':attribute harus terisi.',
            'not_in' => ':attribute harus terisi',
        ],[
            'kode_akun' => 'kode akun',
            'kode_lawan.*' => 'kode lawan',
            'subtotal.*' => 'subtotal',
            'keterangan.*' => 'keterangan'

        ]);

        // PERBAIKAN SAK: kunci periode ditegakkan dan akun posting diperiksa di server
        if ($this->tanggalTerkunci('Kas', $request->tanggal)) {
            return redirect()->back()->withInput()->withError($this->pesanTerkunci('Kas', $request->tanggal));
        }
        $cekAkun = $this->periksaAkunPosting($request->kode_akun, 'Kas');
        if ($cekAkun) {
            return redirect()->back()->withInput()->withError($cekAkun);
        }
        foreach ((array) $request->kode_lawan as $kodeLawan) {
            $cekAkun = $this->periksaAkunPosting($kodeLawan);
            if ($cekAkun) {
                return redirect()->back()->withInput()->withError($cekAkun);
            }
        }
        DB::beginTransaction();
        try {
            $total = 0;
            $loopTotal = $_POST['subtotal'];
            foreach ($loopTotal as $key => $value) {
                $total += $value;
            }
            $kode = $request->tipe == 'Masuk' ? 'BKM' : 'BKK';
            $tahun = date('Y', strtotime($request->tanggal));
            $bulan = date('m', strtotime($request->tanggal));
            $kodeKas = $this->generateNomorTransaksi($kode, $tahun, $bulan, $request->kode_akun);

            $addTransaksi = new TransaksiKas;
            $addTransaksi->kode_transaksi_kas = $kodeKas;
            $addTransaksi->tanggal = $request->tanggal;
            $addTransaksi->akun_kode = $request->kode_akun;
            $addTransaksi->tipe = $request->tipe;
            $addTransaksi->total = $total;


            $addTransaksi->save();

            // User Activity
            $addUserActivity = new UserActivity;
            $addUserActivity->id_user = Auth::user()->id;
            $addUserActivity->jenis_transaksi = 'Kas';
            $addUserActivity->tipe = 'Insert';
            $addUserActivity->keterangan = 'Berhasil menambahkan Transaksi Kas dengan kode '.$kodeKas.' dengan total '.$total.'.';
            $addUserActivity->save();

            foreach ($_POST['subtotal'] as $key => $value) {
                $addDetailKas =  new TransaksiKasDetail;
                $addDetailKas->kode_transaksi_kas = $kodeKas;
                $addDetailKas->kode_lawan = $_POST['kode_lawan'][$key];
                $addDetailKas->subtotal = $_POST['subtotal'][$key];
                $addDetailKas->keterangan = $_POST['keterangan'][$key];

                $addDetailKas->save();

                // tambah jurnal
                $addJurnal = new Jurnal;
                $addJurnal->tanggal = $request->tanggal;
                $addJurnal->jenis_transaksi = 'Kas';
                $addJurnal->kode_transaksi = $kodeKas;
                $addJurnal->keterangan = $_POST['keterangan'][$key];
                $addJurnal->kode = $request->kode_akun;
                $addJurnal->lawan = $_POST['kode_lawan'][$key];
                $addJurnal->tipe = $request->tipe == 'Masuk' ? 'Debit' : 'Kredit';
                $addJurnal->nominal = $_POST['subtotal'][$key];
                $addJurnal->id_detail = $addDetailKas->id;
                $addJurnal->save();
            }
            DB::commit();
            return redirect()->route('kas-transaksi.index')->withStatus('Berhasil Menambahkan data');
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->back()->withError('Terjadi kesalahan.' . $e->getMessage());
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withError('Terjadi kesalahan.'. $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $this->param['btnText'] = 'Kembali';
            $this->param['btnLink'] = route('kas-transaksi.index');
            $this->param['kodeAkun'] = KodeAkun::select('kode_akun.kode_akun', 'kode_akun.nama')
                                                ->join('kode_induk', 'kode_akun.induk_kode', 'kode_induk.kode_induk')
                                                ->where('kode_akun.jenis_akun', 'Kas')
                                                ->get();
            $this->param['kode_lawan'] = KodeAkun::select('kode_akun.kode_akun', 'kode_akun.nama')
                                                ->join('kode_induk', 'kode_akun.induk_kode', 'kode_induk.kode_induk')
                                                ->get();
            $this->param['transaksi_kas'] = TransaksiKas::find($id);
            $this->param['transaksi_kas_detail'] = TransaksiKasDetail::where('kode_transaksi_kas',$id)->get();

            return view('pages.transaksi-kas.show',$this->param);

        } catch (QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan.'. $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $this->param['btnText'] = 'Lihat Transaksi Kas';
            $this->param['btnLink'] = route('kas-transaksi.index');
            $this->param['kodeAkun'] = KodeAkun::select('kode_akun.kode_akun', 'kode_akun.nama')
                                                ->join('kode_induk', 'kode_akun.induk_kode', 'kode_induk.kode_induk')
                                                ->where('kode_akun.jenis_akun', 'Kas')
                                                ->get();
            $this->param['kode_lawan'] = KodeAkun::select('kode_akun.kode_akun', 'kode_akun.nama')
                                                ->join('kode_induk', 'kode_akun.induk_kode', 'kode_induk.kode_induk')
                                                ->get();
            $this->param['transaksi_kas'] = TransaksiKas::find($id);
            $this->param['transaksi_kas_detail'] = TransaksiKasDetail::where('kode_transaksi_kas',$id)->get();

            return view('pages.transaksi-kas.edit',$this->param);

        } catch (QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan.'. $e->getMessage());
        }
    }

    public function addEditDetailKasTransaksi()
    {
        $fields = array(
            'kode_lawan' => 'kode_lawan',
            'subtotal' => 'subtotal',
            'keterangan' => 'keterangan',
        );
        $next = $_GET['biggestNo'] + 1;

        $kode_lawan = KodeAkun::select('kode_akun.kode_akun', 'kode_akun.nama')
            ->join('kode_induk', 'kode_akun.induk_kode', 'kode_induk.kode_induk')
            ->get();
        return view('pages.transaksi-kas.form-edit-detail-transaksi-kas', ['hapus' => true, 'no' => $next, 'kode_lawan' => $kode_lawan, 'fields' => $fields, 'idDetail' => '0']);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'tipe' => 'required|not_in:0',
            'kode_akun' => 'required|not_in:0',
            'kode_lawan.*' => 'required|not_in:0',
            'subtotal.*' => 'required|numeric|gt:0',
            'keterangan.*' => 'required',
        ],[
            'required' => ':attribute harus terisi.',
            'not_in' => ':attribute harus terisi',
        ],[
            'kode_akun' => 'kode akun',
            'kode_lawan.*' => 'kode lawan',
            'subtotal.*' => 'subtotal',
            'keterangan.*' => 'keterangan'

        ]);

        DB::beginTransaction();
        try {

            $transaksi_kas = TransaksiKas::where('kode_transaksi_kas',$id)->get()[0];
            $bulan_transaksi_kas = date('m-Y',strtotime($transaksi_kas->tanggal));
            $edit_bulan_transasik_kas = date('m-Y',strtotime($request->tanggal));

            if ($bulan_transaksi_kas != $edit_bulan_transasik_kas) {
                return redirect()->back()->withStatus('tidak dapat memperbarui bulan transaksi');
            }
            // PERBAIKAN SAK: transaksi terkunci atau di luar periode berjalan tidak dapat diubah langsung
            if ($this->tanggalTerkunci('Kas', $transaksi_kas->tanggal)) {
                return redirect()->back()->withError($this->pesanTerkunci('Kas', $transaksi_kas->tanggal));
            }
            if (!$this->diPeriodeBerjalan($transaksi_kas->tanggal)) {
                return redirect()->back()->withError($this->pesanBukanPeriodeBerjalan($transaksi_kas->tanggal));
            }
            if ($this->tanggalTerkunci('Kas', $request->tanggal)) {
                return redirect()->back()->withError($this->pesanTerkunci('Kas', $request->tanggal));
            }
            $cekAkun = $this->periksaAkunPosting($request->kode_akun, 'Kas');
            if ($cekAkun) {
                return redirect()->back()->withError($cekAkun);
            }
            foreach ((array) $request->kode_lawan as $kodeLawan) {
                $cekAkun = $this->periksaAkunPosting($kodeLawan);
                if ($cekAkun) {
                    return redirect()->back()->withError($cekAkun);
                }
            }
            $tipe = $request->tipe; // PERBAIKAN SAK: tipe jurnal mengikuti tipe BARU dari form edit, bukan tipe lama di database

            $newTotal = 0;

            foreach ($_POST['kode_lawan'] as $key => $value) {
                // cek tambah detail baru
                if ($_POST['id_detail'][$key] != 0) {
                    $get_detail_transaksi_kas = TransaksiKasDetail::select('kode_lawan','keterangan','subtotal')
                                                                    ->where('id',$_POST['id_detail'][$key])->get()[0];
                    // cek terdapat perubahan pada detail
                    if ($_POST['kode_lawan'][$key] != $get_detail_transaksi_kas['kode_lawan'] || $_POST['keterangan'][$key] != $get_detail_transaksi_kas['keterangan'] || $_POST['subtotal'][$key] != $get_detail_transaksi_kas['subtotal']) {
                        // update detail transaksi kas
                        TransaksiKasDetail::where('id', $_POST['id_detail'][$key])
                        ->update([
                            'kode_lawan' => $_POST['kode_lawan'][$key],
                            'subtotal' => $_POST['subtotal'][$key],
                            'keterangan' => $_POST['keterangan'][$key],
                        ]);

                        // update jurnal
                        Jurnal::where('id_detail', $_POST['id_detail'][$key])
                        ->where('kode_transaksi', $id)
                        ->update([
                            'tanggal' => $_POST['tanggal'],
                            'keterangan' => $_POST['keterangan'][$key],
                            'kode' => $request->kode_akun,
                            'lawan' => $_POST['kode_lawan'][$key],
                            'nominal' => $_POST['subtotal'][$key],
                            'tipe' => $request->tipe == 'Masuk' ? 'Debit' : 'Kredit', // PERBAIKAN SAK: sinkronkan sisi jurnal dengan tipe header
                        ]);

                    }else{
                        // update jurnal
                        Jurnal::where('id_detail', $_POST['id_detail'][$key])
                        ->where('kode_transaksi', $id)
                        ->update([
                            'tanggal' => $_POST['tanggal'],
                            'kode' => $request->kode_akun,
                            'tipe' => $request->tipe == 'Masuk' ? 'Debit' : 'Kredit', // PERBAIKAN SAK: sinkronkan sisi jurnal dengan tipe header
                        ]);
                    }
                }else{
                        //insert to detail
                        $newDetailTransaksiKas = TransaksiKasDetail::create([
                            'kode_transaksi_kas' => $_POST['kode_transaksi_kas'],
                            'keterangan' => $_POST['keterangan'][$key],
                            'kode_lawan' => $_POST['kode_lawan'][$key],
                            'subtotal' => $_POST['subtotal'][$key],
                        ]);
                        Jurnal::insert([
                            'tanggal' => $_POST['tanggal'],
                            'jenis_transaksi' => 'Kas',
                            'kode_transaksi' => $_POST['kode_transaksi_kas'],
                            'keterangan' => $_POST['keterangan'][$key],
                            'kode' => $_POST['kode_akun'],
                            'lawan' => $_POST['kode_lawan'][$key],
                            'tipe' => $tipe == 'Masuk' ? 'Debit' : 'Kredit',
                            'nominal' => $_POST['subtotal'][$key],
                            'id_detail' => $newDetailTransaksiKas->id
                        ]);
                }
                $newTotal = $newTotal + $_POST['subtotal'][$key];
            }

            if (isset($_POST['id_delete'])) {
                foreach ($_POST['id_delete'] as $key => $value) {

                    //delete detail
                    TransaksiKasDetail::where('id', $value)->delete();

                    //delete jurnal
                    Jurnal::where('id_detail', $value)->where('kode_transaksi', $id)->delete();
                }
            }

            TransaksiKas::where('kode_transaksi_kas', $id)
            ->update([
                'tanggal' => $_POST['tanggal'],
                'akun_kode' => $_POST['kode_akun'],
                'tipe' => $_POST['tipe'],
                'total' => $newTotal,
            ]);

            // User Activity
            $addUserActivity = new UserActivity;
            $addUserActivity->id_user = Auth::user()->id;
            $addUserActivity->jenis_transaksi = 'Kas';
            $addUserActivity->tipe = 'Update';
            $addUserActivity->keterangan = 'Berhasil memperbarui/menambahkan data baru Transaksi Kas dengan kode '.$_POST['kode_transaksi_kas'].' dengan total '.$newTotal.'.';
            $addUserActivity->save();

            DB::commit();
            return redirect()->route('kas-transaksi.index')->withStatus('Berhasil memperbarui data.');
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->back()->withError('Terjadi kesalahan.' . $e->getMessage());
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withError('Terjadi kesalahan.'. $e->getMessage());
        }
    }

    // softdelete transaksi kas
    public function hapusPermanen($id)
    {
        try {
            $deleteTransaksiKas = TransaksiKas::onlyTrashed()->find($id);
            TransaksiKasDetail::where('kode_transaksi_kas',$id)->delete();
            Jurnal::withTrashed()->where('kode_transaksi',$id)->forceDelete(); // PERBAIKAN SAK: hapus permanen jurnal (termasuk yang soft-deleted)
            // User Activity
            $addUserActivity = new UserActivity;
            $addUserActivity->id_user = Auth::user()->id;
            $addUserActivity->jenis_transaksi = 'Kas';
            $addUserActivity->tipe = 'Delete';
            $addUserActivity->keterangan = 'Menghapus Transaksi Kas dengan kode '.$id.' dengan total '.$deleteTransaksiKas->total.'.';
            $addUserActivity->save();

            $deleteTransaksiKas->forceDelete();
            return redirect()->route('transaksiKas.trash')->withStatus('Data berhasil dihapus permanen.');

        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
        catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
    }

    public function restoretransaksiKas($id)
    {
        try {
            $transaksiKas = TransaksiKas::withTrashed()->findOrFail($id);
            // PERBAIKAN SAK: memulihkan transaksi ke periode terkunci sama dengan mengubah periode itu
            if ($this->tanggalTerkunci('Kas', $transaksiKas->tanggal)) {
                return redirect()->back()->withError($this->pesanTerkunci('Kas', $transaksiKas->tanggal));
            }
            if ($transaksiKas->trashed()) {
                $transaksiKas->deleted_by = null;
                $transaksiKas->restore();
                Jurnal::onlyTrashed()->where('kode_transaksi', $id)->restore(); // PERBAIKAN SAK: jurnal ikut dipulihkan
                return redirect()->route('transaksiKas.trash')->withStatus('Data berhasil di kembalikan.');
            }
            else
            {
                return redirect()->route('transaksiKas.trash')->withError('Data tidak ada dalam sampah.');
            }

        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
        catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
    }

    public function trashTransaksiKas(Request $request)
    {
        $this->param['btnText'] = 'Tambah Data';
        $this->param['btnLink'] = route('kas-transaksi.create');
        try {
            $keyword = $request->get('keyword');
            $getTransaksiKas = TransaksiKas::orderBy('tanggal', 'DESC')->orderBy('created_at', 'DESC')->onlyTrashed();

            if ($keyword) {
                $getTransaksiKas->where('kode_transaksi_kas', 'LIKE', "%{$keyword}%")->orWhere('tipe', 'LIKE', "%{$keyword}%")->orWhere('akun_kode', 'LIKE', "%{$keyword}%");
            }

            $this->param['transaksi_kas'] = $getTransaksiKas->paginate(10);
            return view('pages.transaksi-kas.listTrash', $this->param);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        } catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }

    }
    public function destroy($id)
    {
        try {
            $trashTransaksiKas = TransaksiKas::findOrFail($id);
            // PERBAIKAN SAK: transaksi terkunci atau di luar periode berjalan tidak dapat dipindah ke sampah
            if ($this->tanggalTerkunci('Kas', $trashTransaksiKas->tanggal)) {
                return redirect()->back()->withError($this->pesanTerkunci('Kas', $trashTransaksiKas->tanggal));
            }
            if (!$this->diPeriodeBerjalan($trashTransaksiKas->tanggal)) {
                return redirect()->back()->withError($this->pesanBukanPeriodeBerjalan($trashTransaksiKas->tanggal));
            }
            // return $trashUser;
            if ($trashTransaksiKas->deleted_by = auth()->user()->id) {
                $trashTransaksiKas->update();
            }
            $trashTransaksiKas->delete();
            Jurnal::where('kode_transaksi', $id)->delete(); // PERBAIKAN SAK: jurnal ikut ke sampah (soft delete) agar tidak terhitung di laporan
            return redirect()->route('kas-transaksi.index')->withStatus('Berhasil memindahkan ke sampah');

        } catch (\Exception $e) {
            return $e;
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (\Illuminate\Database\QueryException $e) {
            return $e;
            return redirect()->back()->withError('Terjadi kesalahan.');
        }
    }

    // add detail transaksi kas
    public function DetailKasTransaksi()
    {
        $next = $_GET['biggestNo'] + 1;
        $kode_lawan = KodeAkun::select('kode_akun.kode_akun', 'kode_akun.nama')
            ->join('kode_induk', 'kode_akun.induk_kode', 'kode_induk.kode_induk')
            ->get();
        return view('pages.transaksi-kas.form-detail-transaksi-kas', ['hapus' => true, 'no' => $next, 'kode_lawan' => $kode_lawan]);
    }
    // report kas
    public function reportKas()
    {
        try {
            $this->param['kodeAkun'] = KodeAkun::select('kode_akun.kode_akun', 'kode_akun.nama')
            // ->join('kode_induk', 'kode_akun.induk_kode', 'kode_induk.kode_induk')
                                                ->where('kode_akun.jenis_akun', 'Kas')
                                                ->get();
            $this->param['report_kas'] = null;
            return view('pages.transaksi-kas.laporan-kas',$this->param);
        }catch(\Exception $e){
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        }catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }
    public function getReport(Request $request)
    {
        $request->validate([
            'kode_perkiraan' => 'required|not_in:0',
            'start' => 'required',
            'end' => 'required'
        ],[
            'required', ':atrribute harus terisi',
            'no_in' => ':attribute harus terisi'
        ],[
            'kode_perkiraan' => 'kode perkiraan',
        ]);
        // return $request;
        try {
            $this->param['kodeAkun'] = KodeAkun::select('kode_akun.kode_akun', 'kode_akun.nama')
                                                ->where('kode_akun.jenis_akun', 'Kas')
                                                ->get();
            $this->param['report_kas'] = TransaksiKas::select(
                                                    'transaksi_kas.kode_transaksi_kas',
                                                    'transaksi_kas.tanggal',
                                                    'transaksi_kas.akun_kode',
                                                    'transaksi_kas.tipe',
                                                    'transaksi_kas.total',
                                                    'transaksi_kas_detail.kode_transaksi_kas',
                                                    'transaksi_kas_detail.kode_lawan',
                                                    'transaksi_kas_detail.subtotal',
                                                    'transaksi_kas_detail.keterangan')
                                                    ->join('transaksi_kas_detail','transaksi_kas_detail.kode_transaksi_kas','transaksi_kas.kode_transaksi_kas')
                                                    ->where('transaksi_kas.akun_kode',$request->kode_perkiraan)
                                                    // ->whereBetween('transaksi_kas.tanggal', [$request->get('start'), $request->get('end')])
                                                    ->whereBetween('transaksi_kas.tanggal',[$request->start,$request->end])
                                                    ->get();
            return view('pages.transaksi-kas.laporan-kas',$this->param);
        }catch(\Exception $e){
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        }catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }
    public function printReport(Request $request)
    {
        $request->validate([
            'kode_perkiraan' => 'required|not_in:0',
            'start' => 'required',
            'end' => 'required'
        ],[
            'required', ':atrribute harus terisi',
            'no_in' => ':attribute harus terisi'
        ],[
            'kode_perkiraan' => 'kode perkiraan',
        ]);
        // return $request;
        try {
            $this->param['kodeAkun'] = KodeAkun::select('kode_akun.kode_akun', 'kode_akun.nama')
                                                ->where('kode_akun.jenis_akun', 'Kas')
                                                ->get();
            $this->param['report_kas'] = TransaksiKas::select(
                                                    'transaksi_kas.kode_transaksi_kas',
                                                    'transaksi_kas.tanggal',
                                                    'transaksi_kas.akun_kode',
                                                    'transaksi_kas.tipe',
                                                    'transaksi_kas.total',
                                                    'transaksi_kas_detail.kode_transaksi_kas',
                                                    'transaksi_kas_detail.kode_lawan',
                                                    'transaksi_kas_detail.subtotal',
                                                    'transaksi_kas_detail.keterangan')
                                                    ->join('transaksi_kas_detail','transaksi_kas_detail.kode_transaksi_kas','transaksi_kas.kode_transaksi_kas')
                                                    ->where('transaksi_kas.akun_kode',$request->kode_perkiraan)
                                                    // ->whereBetween('transaksi_kas.tanggal', [$request->get('start'), $request->get('end')])
                                                    ->whereBetween('transaksi_kas.tanggal',[$request->start,$request->end])
                                                    ->get();
            return view('pages.transaksi-kas.print-laporan-kas',$this->param);
        }catch(\Exception $e){
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        }catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }

    // PERBAIKAN SAK: buat Jurnal Koreksi (memorial pembalik) atas transaksi ini
    public function koreksi($id)
    {
        $transaksi = TransaksiKas::findOrFail($id);
        list($berhasil, $pesan) = $this->buatJurnalKoreksi('Kas', $transaksi->kode_transaksi_kas);
        if ($berhasil) {
            return redirect()->route('memorial.index')->withStatus($pesan);
        }
        return redirect()->back()->withError($pesan);
    }
}
