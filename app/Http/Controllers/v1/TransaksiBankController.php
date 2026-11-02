<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\KodeAkun;
use App\Models\TransaksiBank;
use App\Models\TransaksiBankDetail;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\SequenceTrait;
use App\Models\TransaksiKasDetail;
use App\Models\UserActivity;
use Illuminate\Support\Facades\Auth;

class TransaksiBankController extends Controller
{
    private $param;

    use SequenceTrait;

    public function __construct()
    {
        $this->param['pageTitle'] = 'Transaksi Bank / List Transaksi Bank';
        $this->param['pageIcon'] = 'ti-wallet';
        $this->param['parentMenu'] = 'Transaksi Bank';
        $this->param['current'] = 'Transaksi Bank';
    }

    public function index(Request $request)
    {
        $this->param['btnText'] = 'Tambah Data';
        $this->param['btnLink'] = route('bank-transaksi.create');
        try {
            $keyword = $request->get('keyword');
            $getTransaksiBank = TransaksiBank::orderBy('tanggal', 'DESC')->orderBy('created_at', 'DESC');

            if ($keyword) {
                $getTransaksiBank->where('kode_transaksi_bank', 'LIKE', "%{$keyword}%")->orWhere('tipe', 'LIKE', "%{$keyword}%")->orWhere('akun_kode', 'LIKE', "%{$keyword}%");
            }

            $this->param['transaksi_bank'] = $getTransaksiBank->paginate(10);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
        catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }

        return view('pages.transaksi-bank.index', $this->param);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->param['btnText'] = 'Lihat Data';
        $this->param['btnLink'] = route('bank-transaksi.index');
        $this->param['kodeAkun'] = KodeAkun::select('kode_akun.kode_akun','kode_akun.nama')
                                            // ->join('kode_induk','kode_akun.induk_kode','kode_induk.kode_induk')
                                            ->where('kode_akun.nama','LIKE','Bank%')
                                            ->get();
        $this->param['kode_lawan'] = KodeAkun::select('kode_akun.kode_akun','kode_akun.nama')
                                            // ->join('kode_induk','kode_akun.induk_kode','kode_induk.kode_induk')
                                            ->where('kode_akun.nama','!=','Kas')
                                            ->where('kode_akun.nama', '!=', 'Bank')
                                            ->get();
        return view('pages.transaksi-bank.create',$this->param);
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
            'tanggal' => 'required',
            'tipe' => 'required|not_in:0',
            'kode_akun' => 'required|not_in:0',
            'kode_lawan.*' => 'required|not_in:0',
            'subtotal.*' => 'required',
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
        // return $request;
        DB::beginTransaction();
        try {
            $total = 0;
            $loopTotal = $_POST['subtotal'];
            foreach ($loopTotal as $key => $value) {
                $total += $value;
            }

            $kode = $request->tipe == 'Masuk' ? 'BBM' : 'BBK';
            $tahun = date('Y', strtotime($request->tanggal));
            $bulan = date('m', strtotime($request->tanggal));
            $kodeBank = $this->generateNomorTransaksi($kode, $tahun, $bulan, $request->kode_akun);

            $addTransaksi = new TransaksiBank;
            $addTransaksi->kode_transaksi_bank = $kodeBank;
            $addTransaksi->tanggal = $request->tanggal;
            $addTransaksi->akun_kode = $request->kode_akun;
            $addTransaksi->tipe = $request->tipe;
            $addTransaksi->total = $total;


            $addTransaksi->save();

             // User Activity
            $addUserActivity = new UserActivity;
            $addUserActivity->id_user = Auth::user()->id;
            $addUserActivity->jenis_transaksi = 'Bank';
            $addUserActivity->tipe = 'Insert';
            $addUserActivity->keterangan = 'Berhasil menambahkan Transaksi Bank dengan kode '.$kodeBank.' dengan total '.$total.'.';
            $addUserActivity->save();

            foreach ($_POST['subtotal'] as $key => $value) {
                $addDetailBank =  new TransaksiBankDetail;
                $addDetailBank->kode_transaksi_bank = $kodeBank;
                $addDetailBank->kode_lawan = $_POST['kode_lawan'][$key];
                $addDetailBank->subtotal = $_POST['subtotal'][$key];
                $addDetailBank->keterangan = $_POST['keterangan'][$key];

                $addDetailBank->save();

                // tambah jurnal
                $addJurnal = new Jurnal;
                $addJurnal->tanggal = $request->tanggal;
                $addJurnal->jenis_transaksi = 'Bank';
                $addJurnal->kode_transaksi = $kodeBank;
                $addJurnal->keterangan = $_POST['keterangan'][$key];
                $addJurnal->kode = $request->kode_akun;
                $addJurnal->lawan = $_POST['kode_lawan'][$key];
                $addJurnal->tipe = $request->tipe == 'Masuk' ? 'Debit' : 'Kredit';
                $addJurnal->nominal = $_POST['subtotal'][$key];
                $addJurnal->id_detail = $addDetailBank->id;
                $addJurnal->save();
            }
            DB::commit();
            return redirect()->route('bank-transaksi.index')->withStatus('Berhasil Menambahkan data');
         } catch (QueryException $e) {
             DB::rollBack();
            //  return $e;
             return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (Exception $e){
            DB::rollBack();
            // return $e;
            return redirect()->back()->withError('Terjadi kesalahan.');
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
            $this->param['btnText'] = 'Kembaki';
            $this->param['btnLink'] = route('bank-transaksi.index');
            $this->param['kodeAkun'] = KodeAkun::select('kode_akun.kode_akun', 'kode_akun.nama')
                                                ->join('kode_induk', 'kode_akun.induk_kode', 'kode_induk.kode_induk')
                                                ->where('kode_akun.nama', 'LIKE', 'Bank%')
                                                ->get();
            $this->param['kode_lawan'] = KodeAkun::select('kode_akun.kode_akun', 'kode_akun.nama')
                                                ->join('kode_induk', 'kode_akun.induk_kode', 'kode_induk.kode_induk')
                                                ->where('kode_akun.nama', '!=', 'Kas')
                                                ->where('kode_akun.nama', '!=', 'Bank')
                                                ->get();
            $this->param['transaksi_bank'] = TransaksiBank::find($id);
            $this->param['transaksi_bank_detail'] = TransaksiBankDetail::where('kode_transaksi_bank',$id)->get();

            return view('pages.transaksi-bank.show',$this->param);

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
            $this->param['btnText'] = 'Lihat Transaksi Bank';
            $this->param['btnLink'] = route('bank-transaksi.index');
            $this->param['kodeAkun'] = KodeAkun::select('kode_akun.kode_akun', 'kode_akun.nama')
                                                ->join('kode_induk', 'kode_akun.induk_kode', 'kode_induk.kode_induk')
                                                ->where('kode_akun.nama', 'LIKE', 'Bank%')
                                                ->get();
            $this->param['kode_lawan'] = KodeAkun::select('kode_akun.kode_akun', 'kode_akun.nama')
                                                ->join('kode_induk', 'kode_akun.induk_kode', 'kode_induk.kode_induk')
                                                ->where('kode_akun.nama', '!=', 'Kas')
                                                ->where('kode_akun.nama', '!=', 'Bank')
                                                ->get();
            $this->param['transaksi_bank'] = TransaksiBank::find($id);
            $this->param['transaksi_bank_detail'] = TransaksiBankDetail::where('kode_transaksi_bank',$id)->get();

            return view('pages.transaksi-bank.edit',$this->param);

        } catch (QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan.'. $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required',
            'tipe' => 'required|not_in:0',
            'kode_akun' => 'required|not_in:0',
            'kode_lawan.*' => 'required|not_in:0',
            'subtotal.*' => 'required|numeric',
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

            $transaksi_bank = TransaksiBank::where('kode_transaksi_bank',$id)->get()[0];
            $bulan_transaksi_bank = date('m-Y',strtotime($transaksi_bank->tanggal));
            $edit_bulan_transasik_bank = date('m-Y',strtotime($request->tanggal));

            if ($bulan_transaksi_bank != $edit_bulan_transasik_bank) {
                return redirect()->back()->withStatus('tidak dapat memperbarui bulan transaksi');
            }
            $tipe = $request->tipe; // PERBAIKAN SAK: tipe jurnal mengikuti tipe BARU dari form edit, bukan tipe lama di database

            $newTotal = 0;

            foreach ($_POST['kode_lawan'] as $key => $value) {
                // cek tambah detail baru
                if ($_POST['id_detail'][$key] != 0) {
                    $get_detail_transaksi_bank = TransaksiBankDetail::select('kode_lawan','keterangan','subtotal')
                                                                    ->where('id',$_POST['id_detail'][$key])->get()[0];
                    // cek terdapat perubahan pada detail
                    if ($_POST['kode_lawan'][$key] != $get_detail_transaksi_bank['kode_lawan'] || $_POST['keterangan'][$key] != $get_detail_transaksi_bank['keterangan'] || $_POST['subtotal'][$key] != $get_detail_transaksi_bank['subtotal']) {
                        // update detail transaksi bank
                        TransaksiBankDetail::where('id', $_POST['id_detail'][$key])
                        ->update([
                            'kode_lawan' => $_POST['kode_lawan'][$key],
                            'subtotal' => $_POST['subtotal'][$key],
                            'keterangan' => $_POST['keterangan'][$key],
                        ]);

                        // return 'aman';

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
                    $newDetailTransaksiBank = TransaksiBankDetail::create([
                        'kode_transaksi_bank' => $_POST['kode_transaksi_bank'],
                        'keterangan' => $_POST['keterangan'][$key],
                        'kode_lawan' => $_POST['kode_lawan'][$key],
                        'subtotal' => $_POST['subtotal'][$key],
                    ]);
                    // return $_POST['kode_lawan'];

                        Jurnal::insert([
                            'tanggal' => $_POST['tanggal'],
                            'jenis_transaksi' => 'Bank',
                            'kode_transaksi' => $_POST['kode_transaksi_bank'],
                            'keterangan' => $_POST['keterangan'][$key],
                            'kode' => $_POST['kode_akun'],
                            'lawan' => $_POST['kode_lawan'][$key],
                            'tipe' => $tipe == 'Masuk' ? 'Debit' : 'Kredit',
                            'nominal' => $_POST['subtotal'][$key],
                            'id_detail' => $newDetailTransaksiBank->id
                        ]);
                }
                $newTotal = $newTotal + $_POST['subtotal'][$key];
            }

            if (isset($_POST['id_delete'])) {
                foreach ($_POST['id_delete'] as $key => $value) {

                    //delete detail
                    TransaksiBankDetail::where('id', $value)->delete();

                    //delete jurnal
                    Jurnal::where('id_detail', $value)->where('kode_transaksi', $id)->delete();
                }
            }

            TransaksiBank::where('kode_transaksi_bank', $id)
            ->update([
                'tanggal' => $_POST['tanggal'],
                'akun_kode' => $_POST['kode_akun'],
                'tipe' => $_POST['tipe'],
                'total' => $newTotal,
            ]);

            // User Activity
            $addUserActivity = new UserActivity;
            $addUserActivity->id_user = Auth::user()->id;
            $addUserActivity->jenis_transaksi = 'Bank';
            $addUserActivity->tipe = 'Update';
            $addUserActivity->keterangan = 'Berhasil memperbarui/menambahkan data baru Transaksi Bank dengan kode '.$_POST['kode_transaksi_bank'].' dengan total '.$newTotal.'.';
            $addUserActivity->save();

            DB::commit();
            return redirect()->route('bank-transaksi.index')->withStatus('Berhasil memperbarui data.');
        } catch (QueryException $e) {
            return $e;
            DB::rollBack();
            return redirect()->back()->withError('Terjadi kesalahan.' . $e->getMessage());
        } catch (Exception $e) {
            return $e;
            DB::rollBack();
            return redirect()->back()->withError('Terjadi kesalahan.'. $e->getMessage());
        }
    }

    // softdelete transaksi bank
    public function hapusPermanen($id)
    {
        try {
            $deleteTransaksiBank = TransaksiBank::onlyTrashed()->find($id);
            TransaksiBankDetail::where('kode_transaksi_bank',$id)->delete();
            Jurnal::withTrashed()->where('kode_transaksi',$id)->forceDelete(); // PERBAIKAN SAK: hapus permanen jurnal (termasuk yang soft-deleted)
            // User Activity
            $addUserActivity = new UserActivity;
            $addUserActivity->id_user = Auth::user()->id;
            $addUserActivity->jenis_transaksi = 'Bank';
            $addUserActivity->tipe = 'Delete';
            $addUserActivity->keterangan = 'Menghapus Transaksi Bank dengan kode '.$id.' dengan total '.$deleteTransaksiBank->total.'.';
            $addUserActivity->save();

            $deleteTransaksiBank->forceDelete();
            return redirect()->route('transaksiBank.trash')->withStatus('Data berhasil dihapus permanen.');

        } catch (\Illuminate\Database\QueryException $e) {
            return $e;
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
        catch (Exception $e) {
            return $e;
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
    }

    public function restoretransaksiBank($id)
    {
        try {
            $transaksiBank = TransaksiBank::withTrashed()->findOrFail($id);
            if ($transaksiBank->trashed()) {
                $transaksiBank->deleted_by = null;
                $transaksiBank->restore();
                Jurnal::onlyTrashed()->where('kode_transaksi', $id)->restore(); // PERBAIKAN SAK: jurnal ikut dipulihkan
                return redirect()->route('transaksiBank.trash')->withStatus('Data berhasil di kembalikan.');
            }
            else
            {
                return redirect()->route('transaksiBank.trash')->withError('Data tidak ada dalam sampah.');
            }

        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
        catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
    }

    public function trashTransaksiBank(Request $request)
    {
        $this->param['btnText'] = 'Tambah Data';
        $this->param['btnLink'] = route('bank-transaksi.create');
        try {
            $keyword = $request->get('keyword');
            $getTransaksiBank = TransaksiBank::orderBy('tanggal', 'DESC')->orderBy('created_at', 'DESC')->onlyTrashed();

            if ($keyword) {
                $getTransaksiBank->where('kode_transaksi_bank', 'LIKE', "%{$keyword}%")->orWhere('tipe', 'LIKE', "%{$keyword}%")->orWhere('akun_kode', 'LIKE', "%{$keyword}%");
            }

            $this->param['transaksi_bank'] = $getTransaksiBank->paginate(10);
            return view('pages.transaksi-bank.listTrash', $this->param);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        } catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
    }
    public function destroy($id)
    {
        try {
            $trashTransaksiBank = TransaksiBank::findOrFail($id);
            // return $trashUser;
            if ($trashTransaksiBank->deleted_by = auth()->user()->id) {
                $trashTransaksiBank->update();
            }
            $trashTransaksiBank->delete();
            Jurnal::where('kode_transaksi', $id)->delete(); // PERBAIKAN SAK: jurnal ikut ke sampah (soft delete) agar tidak terhitung di laporan
            return redirect()->route('bank-transaksi.index')->withStatus('Berhasil memindahkan ke sampah');

        } catch (\Exception $e) {
            return $e;
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (\Illuminate\Database\QueryException $e) {
            return $e;
            return redirect()->back()->withError('Terjadi kesalahan.');
        }
    }
    // detail transaksi bank
    public function DetailbankTransaksi()
    {
        $next = $_GET['biggestNo'] + 1;
        $kode_lawan = KodeAkun::select('kode_akun.kode_akun','kode_akun.nama')
                        ->join('kode_induk','kode_akun.induk_kode','kode_induk.kode_induk')
                        ->where('kode_akun.nama','!=','Kas')
                        ->where('kode_akun.nama', '!=', 'Bank')
                        ->get();
        return view('pages.transaksi-bank.form-detail-transaksi-bank', ['hapus' => true, 'no' => $next, 'kode_lawan' => $kode_lawan]);
    }

    // report Bank
    public function reportBank()
    {
        try {
            $this->param['kodeAkun'] = KodeAkun::select('kode_akun.kode_akun', 'kode_akun.nama')
            // ->join('kode_induk', 'kode_akun.induk_kode', 'kode_induk.kode_induk')
                                                ->where('kode_akun.nama', 'LIKE', 'Bank%')
                                                ->get();
            $this->param['report_bank'] = null;
            return view('pages.transaksi-bank.laporan-bank',$this->param);
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
                                                ->where('kode_akun.nama', 'LIKE', 'Bank%')
                                                ->get();
            $this->param['report_bank'] = TransaksiBank::select(
                                                    'transaksi_bank.kode_transaksi_bank',
                                                    'transaksi_bank.tanggal',
                                                    'transaksi_bank.akun_kode',
                                                    'transaksi_bank.tipe',
                                                    'transaksi_bank.total',
                                                    'transaksi_bank_detail.kode_transaksi_bank',
                                                    'transaksi_bank_detail.kode_lawan',
                                                    'transaksi_bank_detail.subtotal',
                                                    'transaksi_bank_detail.keterangan')
                                                    ->join('transaksi_bank_detail','transaksi_bank_detail.kode_transaksi_bank','transaksi_bank.kode_transaksi_bank')
                                                    ->where('transaksi_bank.akun_kode',$request->kode_perkiraan)
                                                    // ->whereBetween('transaksi_bank.tanggal', [$request->get('start'), $request->get('end')])
                                                    ->whereBetween('transaksi_bank.tanggal',[$request->start,$request->end])
                                                    ->get();
            return view('pages.transaksi-bank.laporan-bank',$this->param);
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
                                                ->where('kode_akun.nama', 'LIKE', 'Kas%')
                                                ->get();
            $this->param['report_kas'] = TransaksiBank::select(
                                                    'transaksi_bank.kode_transaksi_bank',
                                                    'transaksi_bank.tanggal',
                                                    'transaksi_bank.akun_kode',
                                                    'transaksi_bank.tipe',
                                                    'transaksi_bank.total',
                                                    'transaksi_bank_detail.kode_transaksi_bank',
                                                    'transaksi_bank_detail.kode_lawan',
                                                    'transaksi_bank_detail.subtotal',
                                                    'transaksi_bank_detail.keterangan')
                                                    ->join('transaksi_bank_detail','transaksi_bank_detail.kode_transaksi_bank','transaksi_bank.kode_transaksi_bank')
                                                    ->where('transaksi_bank.akun_kode',$request->kode_perkiraan)
                                                    // ->whereBetween('transaksi_bank.tanggal', [$request->get('start'), $request->get('end')])
                                                    ->whereBetween('transaksi_bank.tanggal',[$request->start,$request->end])
                                                    ->get();
            return view('pages.transaksi-bank.print-laporan-bank',$this->param);
        }catch(\Exception $e){
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        }catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }

    // edit kas transaksi
    public function addEditDetailBankTransaksi()
    {
        $fields = array(
            'kode_lawan' => 'kode_lawan',
            'subtotal' => 'subtotal',
            'keterangan' => 'keterangan',
        );
        $next = $_GET['biggestNo'] + 1;

        $kode_lawan = KodeAkun::select('kode_akun.kode_akun', 'kode_akun.nama')
                                ->join('kode_induk', 'kode_akun.induk_kode', 'kode_induk.kode_induk')
                                ->where('kode_akun.nama', '!=', 'Kas')
                                ->where('kode_akun.nama', '!=', 'Bank')
                                ->get();
        return view('pages.transaksi-bank.form-edit-detail-transaksi-bank', ['hapus' => true, 'no' => $next, 'kode_lawan' => $kode_lawan, 'fields' => $fields, 'idDetail' => '0']);
    }
}
