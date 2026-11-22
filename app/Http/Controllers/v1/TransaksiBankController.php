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
use App\Http\Traits\PeriodeGuard;
use App\Http\Traits\JurnalKoreksi;
use App\Models\TransaksiKasDetail;
use App\Models\UserActivity;
use Illuminate\Support\Facades\Auth;

class TransaksiBankController extends Controller
{
    private $param;

    use SequenceTrait, PeriodeGuard, JurnalKoreksi;

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
            report($e);
            return back()->withError('Terjadi kesalahan.');
        }
        catch (Exception $e) {
            report($e);
            return back()->withError('Terjadi kesalahan.');
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
                                            ->where('kode_akun.jenis_akun','Bank')
                                            ->get();
        $this->param['kode_lawan'] = KodeAkun::select('kode_akun.kode_akun','kode_akun.nama')
                                            // ->join('kode_induk','kode_akun.induk_kode','kode_induk.kode_induk')
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
        // return $request;
        // PERBAIKAN SAK: kunci periode ditegakkan dan akun posting diperiksa di server
        if ($this->tanggalTerkunci('Bank Memorial', $request->tanggal)) {
            return redirect()->back()->withInput()->withError($this->pesanTerkunci('Bank Memorial', $request->tanggal));
        }
        $cekAkun = $this->periksaAkunPosting($request->kode_akun, 'Bank');
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
            $loopTotal = $request->subtotal;
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
            $addTransaksi->no_referensi = $request->no_referensi;
            $addTransaksi->total = $total;


            $addTransaksi->save();

             // User Activity
            $addUserActivity = new UserActivity;
            $addUserActivity->id_user = Auth::user()->id;
            $addUserActivity->jenis_transaksi = 'Bank';
            $addUserActivity->tipe = 'Insert';
            $addUserActivity->keterangan = 'Berhasil menambahkan Transaksi Bank dengan kode '.$kodeBank.' dengan total '.$total.'.';
            $addUserActivity->save();

            foreach ($request->subtotal as $key => $value) {
                $addDetailBank =  new TransaksiBankDetail;
                $addDetailBank->kode_transaksi_bank = $kodeBank;
                $addDetailBank->kode_lawan = $request->kode_lawan[$key];
                $addDetailBank->subtotal = $request->subtotal[$key];
                $addDetailBank->keterangan = $request->keterangan[$key];

                $addDetailBank->save();

                // tambah jurnal
                $addJurnal = new Jurnal;
                $addJurnal->tanggal = $request->tanggal;
                $addJurnal->jenis_transaksi = 'Bank';
                $addJurnal->kode_transaksi = $kodeBank;
                $addJurnal->keterangan = $request->keterangan[$key];
                $addJurnal->kode = $request->kode_akun;
                $addJurnal->lawan = $request->kode_lawan[$key];
                $addJurnal->tipe = $request->tipe == 'Masuk' ? 'Debit' : 'Kredit';
                $addJurnal->nominal = $request->subtotal[$key];
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
                                                ->where('kode_akun.jenis_akun', 'Bank')
                                                ->get();
            $this->param['kode_lawan'] = KodeAkun::select('kode_akun.kode_akun', 'kode_akun.nama')
                                                ->join('kode_induk', 'kode_akun.induk_kode', 'kode_induk.kode_induk')
                                                ->get();
            $this->param['transaksi_bank'] = TransaksiBank::find($id);
            $this->param['transaksi_bank_detail'] = TransaksiBankDetail::where('kode_transaksi_bank',$id)->get();

            return view('pages.transaksi-bank.show',$this->param);

        } catch (QueryException $e) {
            report($e);
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (Exception $e) {
            report($e);
            return redirect()->back()->withError('Terjadi kesalahan.');
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
                                                ->where('kode_akun.jenis_akun', 'Bank')
                                                ->get();
            $this->param['kode_lawan'] = KodeAkun::select('kode_akun.kode_akun', 'kode_akun.nama')
                                                ->join('kode_induk', 'kode_akun.induk_kode', 'kode_induk.kode_induk')
                                                ->get();
            $this->param['transaksi_bank'] = TransaksiBank::find($id);
            $this->param['transaksi_bank_detail'] = TransaksiBankDetail::where('kode_transaksi_bank',$id)->get();

            return view('pages.transaksi-bank.edit',$this->param);

        } catch (QueryException $e) {
            report($e);
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (Exception $e) {
            report($e);
            return redirect()->back()->withError('Terjadi kesalahan.');
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

            $transaksi_bank = TransaksiBank::where('kode_transaksi_bank',$id)->get()[0];
            $bulan_transaksi_bank = date('m-Y',strtotime($transaksi_bank->tanggal));
            $edit_bulan_transasik_bank = date('m-Y',strtotime($request->tanggal));

            if ($bulan_transaksi_bank != $edit_bulan_transasik_bank) {
                return redirect()->back()->withStatus('tidak dapat memperbarui bulan transaksi');
            }
            // PERBAIKAN SAK: transaksi terkunci atau di luar periode berjalan tidak dapat diubah langsung
            if ($this->tanggalTerkunci('Bank Memorial', $transaksi_bank->tanggal)) {
                return redirect()->back()->withError($this->pesanTerkunci('Bank Memorial', $transaksi_bank->tanggal));
            }
            if (!$this->diPeriodeBerjalan($transaksi_bank->tanggal)) {
                return redirect()->back()->withError($this->pesanBukanPeriodeBerjalan($transaksi_bank->tanggal));
            }
            if ($this->tanggalTerkunci('Bank Memorial', $request->tanggal)) {
                return redirect()->back()->withError($this->pesanTerkunci('Bank Memorial', $request->tanggal));
            }
            $cekAkun = $this->periksaAkunPosting($request->kode_akun, 'Bank');
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

            foreach ($request->kode_lawan as $key => $value) {
                // cek tambah detail baru
                if ($request->id_detail[$key] != 0) {
                    $get_detail_transaksi_bank = TransaksiBankDetail::select('kode_lawan','keterangan','subtotal')
                                                                    ->where('id',$request->id_detail[$key])->get()[0];
                    // cek terdapat perubahan pada detail
                    if ($request->kode_lawan[$key] != $get_detail_transaksi_bank['kode_lawan'] || $request->keterangan[$key] != $get_detail_transaksi_bank['keterangan'] || $request->subtotal[$key] != $get_detail_transaksi_bank['subtotal']) {
                        // update detail transaksi bank
                        TransaksiBankDetail::where('id', $request->id_detail[$key])
                        ->update([
                            'kode_lawan' => $request->kode_lawan[$key],
                            'subtotal' => $request->subtotal[$key],
                            'keterangan' => $request->keterangan[$key],
                        ]);

                        // return 'aman';

                        // update jurnal
                        Jurnal::where('id_detail', $request->id_detail[$key])
                        ->where('kode_transaksi', $id)
                        ->update([
                            'tanggal' => $request->tanggal,
                            'keterangan' => $request->keterangan[$key],
                            'kode' => $request->kode_akun,
                            'lawan' => $request->kode_lawan[$key],
                            'nominal' => $request->subtotal[$key],
                            'tipe' => $request->tipe == 'Masuk' ? 'Debit' : 'Kredit', // PERBAIKAN SAK: sinkronkan sisi jurnal dengan tipe header
                        ]);

                    }else{

                        // update jurnal
                        Jurnal::where('id_detail', $request->id_detail[$key])
                        ->where('kode_transaksi', $id)
                        ->update([
                            'tanggal' => $request->tanggal,
                            'kode' => $request->kode_akun,
                            'tipe' => $request->tipe == 'Masuk' ? 'Debit' : 'Kredit', // PERBAIKAN SAK: sinkronkan sisi jurnal dengan tipe header
                        ]);
                    }
                }else{
                    //insert to detail
                    $newDetailTransaksiBank = TransaksiBankDetail::create([
                        'kode_transaksi_bank' => $request->kode_transaksi_bank,
                        'keterangan' => $request->keterangan[$key],
                        'kode_lawan' => $request->kode_lawan[$key],
                        'subtotal' => $request->subtotal[$key],
                    ]);
                    // return $request->kode_lawan;

                        Jurnal::insert([
                            'tanggal' => $request->tanggal,
                            'jenis_transaksi' => 'Bank',
                            'kode_transaksi' => $request->kode_transaksi_bank,
                            'keterangan' => $request->keterangan[$key],
                            'kode' => $request->kode_akun,
                            'lawan' => $request->kode_lawan[$key],
                            'tipe' => $tipe == 'Masuk' ? 'Debit' : 'Kredit',
                            'nominal' => $request->subtotal[$key],
                            'id_detail' => $newDetailTransaksiBank->id
                        ]);
                }
                $newTotal = $newTotal + $request->subtotal[$key];
            }

            if ($request->has('id_delete')) {
                foreach ($request->id_delete as $key => $value) {

                    //delete detail
                    TransaksiBankDetail::where('id', $value)->delete();

                    //delete jurnal
                    Jurnal::where('id_detail', $value)->where('kode_transaksi', $id)->delete();
                }
            }

            TransaksiBank::where('kode_transaksi_bank', $id)
            ->update([
                'tanggal' => $request->tanggal,
                'akun_kode' => $request->kode_akun,
                'tipe' => $request->tipe,
                'no_referensi' => $request->no_referensi ?? null,
                'total' => $newTotal,
            ]);

            // User Activity
            $addUserActivity = new UserActivity;
            $addUserActivity->id_user = Auth::user()->id;
            $addUserActivity->jenis_transaksi = 'Bank';
            $addUserActivity->tipe = 'Update';
            $addUserActivity->keterangan = 'Berhasil memperbarui/menambahkan data baru Transaksi Bank dengan kode '.$request->kode_transaksi_bank.' dengan total '.$newTotal.'.';
            $addUserActivity->save();

            DB::commit();
            return redirect()->route('bank-transaksi.index')->withStatus('Berhasil memperbarui data.');
        } catch (QueryException $e) {
            return $e;
            DB::rollBack();
            report($e);
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (Exception $e) {
            return $e;
            DB::rollBack();
            report($e);
            return redirect()->back()->withError('Terjadi kesalahan.');
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
            report($e);
            return back()->withError('Terjadi kesalahan.');
        }
        catch (Exception $e) {
            return $e;
            report($e);
            return back()->withError('Terjadi kesalahan.');
        }
    }

    public function restoretransaksiBank($id)
    {
        try {
            $transaksiBank = TransaksiBank::withTrashed()->findOrFail($id);
            // PERBAIKAN SAK: memulihkan transaksi ke periode terkunci sama dengan mengubah periode itu
            if ($this->tanggalTerkunci('Bank Memorial', $transaksiBank->tanggal)) {
                return redirect()->back()->withError($this->pesanTerkunci('Bank Memorial', $transaksiBank->tanggal));
            }
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
            report($e);
            return back()->withError('Terjadi kesalahan.');
        }
        catch (Exception $e) {
            report($e);
            return back()->withError('Terjadi kesalahan.');
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
            report($e);
            return back()->withError('Terjadi kesalahan.');
        } catch (Exception $e) {
            report($e);
            return back()->withError('Terjadi kesalahan.');
        }
    }
    public function destroy($id)
    {
        try {
            $trashTransaksiBank = TransaksiBank::findOrFail($id);
            // PERBAIKAN SAK: transaksi terkunci atau di luar periode berjalan tidak dapat dipindah ke sampah
            if ($this->tanggalTerkunci('Bank Memorial', $trashTransaksiBank->tanggal)) {
                return redirect()->back()->withError($this->pesanTerkunci('Bank Memorial', $trashTransaksiBank->tanggal));
            }
            if (!$this->diPeriodeBerjalan($trashTransaksiBank->tanggal)) {
                return redirect()->back()->withError($this->pesanBukanPeriodeBerjalan($trashTransaksiBank->tanggal));
            }
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
    public function DetailbankTransaksi(Request $request)
    {
        $next = (int) $request->query('biggestNo', 0) + 1;
        $kode_lawan = KodeAkun::select('kode_akun.kode_akun','kode_akun.nama')
                        ->join('kode_induk','kode_akun.induk_kode','kode_induk.kode_induk')
                        ->get();
        return view('pages.transaksi-bank.form-detail-transaksi-bank', ['hapus' => true, 'no' => $next, 'kode_lawan' => $kode_lawan]);
    }

    // report Bank
    public function reportBank()
    {
        try {
            $this->param['kodeAkun'] = KodeAkun::select('kode_akun.kode_akun', 'kode_akun.nama')
            // ->join('kode_induk', 'kode_akun.induk_kode', 'kode_induk.kode_induk')
                                                ->where('kode_akun.jenis_akun', 'Bank')
                                                ->get();
            $this->param['report_bank'] = null;
            return view('pages.transaksi-bank.laporan-bank',$this->param);
        }catch(\Exception $e){
            report($e);
            return redirect()->back()->withStatus('Terjadi kesalahan.');
        }catch(\Illuminate\Database\QueryException $e){
            report($e);
            return redirect()->back()->withStatus('Terjadi kesalahan.');
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
                                                ->where('kode_akun.jenis_akun', 'Bank')
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
            report($e);
            return redirect()->back()->withStatus('Terjadi kesalahan.');
        }catch(\Illuminate\Database\QueryException $e){
            report($e);
            return redirect()->back()->withStatus('Terjadi kesalahan.');
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
                                                ->where('kode_akun.jenis_akun', 'Bank')
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
            report($e);
            return redirect()->back()->withStatus('Terjadi kesalahan.');
        }catch(\Illuminate\Database\QueryException $e){
            report($e);
            return redirect()->back()->withStatus('Terjadi kesalahan.');
        }
    }

    // edit kas transaksi
    public function addEditDetailBankTransaksi(Request $request)
    {
        $fields = array(
            'kode_lawan' => 'kode_lawan',
            'subtotal' => 'subtotal',
            'keterangan' => 'keterangan',
        );
        $next = (int) $request->query('biggestNo', 0) + 1;

        $kode_lawan = KodeAkun::select('kode_akun.kode_akun', 'kode_akun.nama')
                                ->join('kode_induk', 'kode_akun.induk_kode', 'kode_induk.kode_induk')
                                ->get();
        return view('pages.transaksi-bank.form-edit-detail-transaksi-bank', ['hapus' => true, 'no' => $next, 'kode_lawan' => $kode_lawan, 'fields' => $fields, 'idDetail' => '0']);
    }

    // PERBAIKAN SAK: buat Jurnal Koreksi (memorial pembalik) atas transaksi ini
    public function koreksi($id)
    {
        $transaksi = TransaksiBank::findOrFail($id);
        list($berhasil, $pesan) = $this->buatJurnalKoreksi('Bank', $transaksi->kode_transaksi_bank);
        if ($berhasil) {
            return redirect()->route('memorial.index')->withStatus($pesan);
        }
        return redirect()->back()->withError($pesan);
    }
}
