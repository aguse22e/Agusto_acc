<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Traits\SequenceTrait;
use App\Models\Jurnal;
use App\Models\KodeAkun;
use App\Models\Memorial;
use App\Models\MemorialDetail;
use App\Models\UserActivity;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MemorialController extends Controller
{
    private $param;

    use SequenceTrait;

    public function __construct()
    {
        $this->param['pageTitle'] = 'Memorial / List Memorial';
        $this->param['pageIcon'] = 'ti-wallet';
        $this->param['parentMenu'] = 'Memorial';
        $this->param['current'] = 'Memorial';
    }
    public function index(Request $request)
    {
        $this->param['btnText'] = 'Tambah Data';
        $this->param['btnLink'] = route('memorial.create');
        try {
            $keyword = $request->get('keyword');
            $getMemorial = Memorial::orderBy('tanggal', 'DESC')->orderBy('created_at', 'DESC');

            if ($keyword) {
                $getMemorial->where('kode_memorial', 'LIKE', "%{$keyword}%")->orWhere('tipe', 'LIKE', "%{$keyword}%");
            }

            $this->param['memorial'] = $getMemorial->paginate(10);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        } catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }

        return view('pages.memorial.index', $this->param);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->param['btnText'] = 'Lihat Data';
        $this->param['btnLink'] = route('memorial.index');

        $this->param['kode_lawan'] = KodeAkun::select('kode_akun.kode_akun','kode_akun.nama')->get();
        return view('pages.memorial.create',$this->param);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request;
        $request->validate([
            'tanggal' => 'required',
            'tipe' => 'required|not_in:0',
            'kode_akun.*' => 'required|not_in:0',
            'kode_lawan.*' => 'required|not_in:0',
            'subtotal.*' => 'required',
            'keterangan.*' => 'required',
        ],[
            'required' => ':attribute harus terisi.',
            'not_in' => ':attribute harus terisi',
        ],[
            'kode_akun.*' => 'kode akun',
            'kode_lawan.*' => 'kode lawan',
            'subtotal.*' => 'subtotal',
            'keterangan.*' => 'keterangan'

        ]);
        DB::beginTransaction();
        try {
            $total = 0;
            $loopTotal = $_POST['subtotal'];
            foreach ($loopTotal as $key => $value) {
                $total += $value;
            }

            $kode = $request->tipe == 'Masuk' ? 'BMM' : 'BMK';
            $tahun = date('Y', strtotime($request->tanggal));
            $bulan = date('m', strtotime($request->tanggal));
            $kodeMemorial = $this->generateNomorTransaksi($kode, $tahun, $bulan, null);

            $addMemorial = new Memorial;
            $addMemorial->kode_memorial = $kodeMemorial;
            $addMemorial->tanggal = $request->tanggal;
            // $addMemorial->akun_kode = $request->kode_akun;
            $addMemorial->tipe = $request->tipe;
            $addMemorial->total = $total;


            $addMemorial->save();

            // User Activity
            $addUserActivity = new UserActivity;
            $addUserActivity->id_user = Auth::user()->id;
            $addUserActivity->jenis_transaksi = 'Memorial';
            $addUserActivity->tipe = 'Insert';
            $addUserActivity->keterangan = 'Berhasil menambahkan Memorial dengan kode '.$kodeMemorial.' dengan total '.$total.'.';
            $addUserActivity->save();


            foreach ($_POST['subtotal'] as $key => $value) {

                $addDetailMemorial =  new MemorialDetail;
                $addDetailMemorial->kode_memorial = $kodeMemorial;
                $addDetailMemorial->keterangan = $_POST['keterangan'][$key];
                $addDetailMemorial->kode = $_POST['kode_akun'][$key];
                $addDetailMemorial->lawan = $_POST['kode_lawan'][$key];
                $addDetailMemorial->subtotal = $_POST['subtotal'][$key];

                $addDetailMemorial->save();

                // tambah jurnal
                $addJurnal = new Jurnal;
                $addJurnal->tanggal = $request->tanggal;
                $addJurnal->jenis_transaksi = 'Memorial';
                $addJurnal->kode_transaksi = $kodeMemorial;
                $addJurnal->keterangan = $_POST['keterangan'][$key];
                $addJurnal->kode = $_POST['kode_akun'][$key];
                $addJurnal->lawan = $_POST['kode_lawan'][$key];
                $addJurnal->tipe = $request->tipe == 'Masuk' ? 'Debit' : 'Kredit';
                $addJurnal->nominal = $_POST['subtotal'][$key];
                $addJurnal->id_detail = $addDetailMemorial->id;
                $addJurnal->save();
            }
            DB::commit();
            return redirect()->route('memorial.index')->withStatus('Berhasil Menambahkan data');
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
            $this->param['btnText'] = 'Lihat Memorial';
            $this->param['btnLink'] = route('memorial.index');
            $this->param['kodeAkun'] = KodeAkun::select('kode_akun.kode_akun','kode_akun.nama')->get();

            $this->param['memorial'] = Memorial::find($id);
            $this->param['detailMemorial'] = MemorialDetail::where('kode_memorial',$id)->get();

            return view('pages.memorial.show',$this->param);

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
            $this->param['btnText'] = 'Lihat Memorial';
            $this->param['btnLink'] = route('memorial.index');
            $this->param['kodeAkun'] = KodeAkun::select('kode_akun.kode_akun','kode_akun.nama')->get();

            $this->param['memorial'] = Memorial::find($id);
            $this->param['detailMemorial'] = MemorialDetail::where('kode_memorial',$id)->get();

            return view('pages.memorial.edit',$this->param);

        } catch (QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan.'. $e->getMessage());
        }
    }

    public function addEditDetailMemorial()
    {
        $fields = array(
            'kode' => 'kode',
            'lawan' => 'lawan',
            'subtotal' => 'subtotal',
            'keterangan' => 'keterangan',
        );
        $next = $_GET['biggestNo'] + 1;
        $kodeAkun = KodeAkun::select('kode_akun.kode_akun', 'kode_akun.nama')
                                ->join('kode_induk', 'kode_akun.induk_kode', 'kode_induk.kode_induk')
                                ->where('kode_akun.nama', '!=', 'Kas')
                                ->where('kode_akun.nama', '!=', 'Bank')
                                ->get();
        return view('pages.memorial.form-edit-detail-memorial', ['hapus' => true, 'no' => $next, 'kodeAkun' => $kodeAkun, 'fields' => $fields, 'idDetail' => '0']);
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
            'kode.*' => 'required|not_in:0',
            'lawan.*' => 'required|not_in:0',
            'subtotal.*' => 'required',
            'keterangan.*' => 'required',
        ],[
            'required' => ':attribute harus terisi.',
            'not_in' => ':attribute harus terisi',
        ],[
            'kode.*' => 'kode akun',
            'lawan.*' => 'kode lawan',
            'subtotal.*' => 'subtotal',
            'keterangan.*' => 'keterangan'

        ]);

        try {
            $memorial = Memorial::where('kode_memorial',$id)->get()[0];

            $bulanMemorial = date('m-Y', strtotime($memorial->tanggal));
            $editBulanMemorial = date('m-Y', strtotime($request->tanggal));

            if ($bulanMemorial != $editBulanMemorial) {
                return redirect()->back()->withStatus('Tidak dapat merubah bulan transaksi');
            }

            $newTotal = 0;
            $tipe = $request->tipe; // PERBAIKAN SAK: tipe jurnal mengikuti tipe BARU dari form edit, bukan tipe lama di database
            foreach ($_POST['lawan'] as $key => $value) {

                if ($_POST['id_detail'][$key] != 0) {
                    $getDetail = MemorialDetail::select('kode','lawan', 'keterangan', 'subtotal')->where('id', $_POST['id_detail'][$key])->get()[0];
                    if ($_POST['kode'][$key] != $getDetail['kode'] || $_POST['lawan'][$key] != $getDetail['lawan'] || $_POST['keterangan'][$key]  != $getDetail['keterangan'] || $_POST['subtotal'][$key] != $getDetail['subtotal']) {
                        //update detail
                        MemorialDetail::where('id', $_POST['id_detail'][$key])
                        ->update([
                            'kode' => $_POST['kode'][$key],
                            'lawan' => $_POST['lawan'][$key],
                            'subtotal' => $_POST['subtotal'][$key],
                            'keterangan' => $_POST['keterangan'][$key],
                        ]);

                    // update jurnal
                        Jurnal::where('id_detail', $_POST['id_detail'][$key])
                        ->where('kode_transaksi', $id)
                        ->update([
                            'tanggal' => $_POST['tanggal'],
                            'keterangan' => $_POST['keterangan'][$key],
                            'kode' => $_POST['kode'][$key],
                            'lawan' => $_POST['lawan'][$key],
                            'nominal' => $_POST['subtotal'][$key],
                            'tipe' => $request->tipe == 'Masuk' ? 'Debit' : 'Kredit', // PERBAIKAN SAK: sinkronkan sisi jurnal dengan tipe header
                        ]);

                    }
                    else{ //hanya mengupdate jurnal
                        // update jurnal
                        Jurnal::where('id_detail', $_POST['id_detail'][$key])
                        ->where('kode_transaksi', $id)
                        ->update([
                            'tanggal' => $_POST['tanggal'],
                            'tipe' => $request->tipe == 'Masuk' ? 'Debit' : 'Kredit', // PERBAIKAN SAK: sinkronkan sisi jurnal dengan tipe header
                        ]);
                    }
                }else{
                    //insert to detail
                    $newDetail = MemorialDetail::create([
                        'kode_memorial' => $_POST['kode_memorial'],
                        'keterangan' => str_replace(' ', '-', strtoupper($_POST['keterangan'][$key])),
                        'kode' => $_POST['kode'][$key],
                        'lawan' => $_POST['lawan'][$key],
                        'subtotal' => $_POST['subtotal'][$key],
                    ]);

                    // update kartu stock
                    Jurnal::insert([
                        'tanggal' => $_POST['tanggal'],
                        'jenis_transaksi' => 'Memorial',
                        'kode_transaksi' => $_POST['kode_memorial'],
                        'keterangan' => str_replace(' ', '-', strtoupper($_POST['keterangan'][$key])),
                        'kode' => $_POST['kode'][$key],
                        'lawan' => $_POST['lawan'][$key],
                        'tipe' => $tipe == 'Masuk' ? 'Debit' : 'Kredit',
                        'nominal' => $_POST['subtotal'][$key],
                        'id_detail' => $newDetail->id
                    ]);
                }
                $newTotal = $newTotal + $_POST['subtotal'][$key];

            }

            if (isset($_POST['id_delete'])) {
                foreach ($_POST['id_delete'] as $key => $value) {

                    //delete detail
                    MemorialDetail::where('id', $value)->delete();

                    //delete kartu stock
                    Jurnal::where('id_detail', $value)->where('kode_transaksi', $id)->delete();
                }
            }
            //update memorial
            Memorial::where('kode_memorial', $id)
            ->update([
                'tanggal' => $_POST['tanggal'],
                'tipe' => $_POST['tipe'],
                'total' => $newTotal,
            ]);
            $addUserActivity = new UserActivity;
            $addUserActivity->id_user = Auth::user()->id;
            $addUserActivity->jenis_transaksi = 'Memorial';
            $addUserActivity->tipe = 'Update';
            $addUserActivity->keterangan = 'Berhasil memperbarui/menambahkan data baru Memorial Kas dengan kode '.$id.' dengan total '.$newTotal.'.';
            $addUserActivity->save();
            return redirect()->route('memorial.index')->withStatus('Data berhasil diperbarui.');
        } catch (QueryException $e) {
            return $e;
            return redirect()->back()->withError('Terjadi kesalahan.' . $e->getMessage());
        } catch (Exception $e) {
            return $e;
            return redirect()->back()->withError('Terjadi kesalahan.'. $e->getMessage());
        }
    }

    // softdelete transaksi bank
    public function hapusPermanen($id)
    {
        try {
            $deleteMemorial = Memorial::onlyTrashed()->find($id);
            MemorialDetail::where('kode_memorial',$id)->delete();
            Jurnal::withTrashed()->where('kode_transaksi',$id)->forceDelete(); // PERBAIKAN SAK: hapus permanen jurnal (termasuk yang soft-deleted)
            // User Activity
            $addUserActivity = new UserActivity;
            $addUserActivity->id_user = Auth::user()->id;
            $addUserActivity->jenis_transaksi = 'Memorial';
            $addUserActivity->tipe = 'Delete';
            $addUserActivity->keterangan = 'Menghapus Transaksi Memorial dengan kode '.$id.' dengan total '.$deleteMemorial->total.'.';
            $addUserActivity->save();

            $deleteMemorial->forceDelete();
            return redirect()->route('transaksiMemorial.trash')->withStatus('Data berhasil dihapus permanen.');

        } catch (\Illuminate\Database\QueryException $e) {
            return $e;
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
        catch (Exception $e) {
            return $e;
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
    }

    public function trashTransaksiMemorial(Request $request)
    {
        $this->param['btnText'] = 'Tambah Data';
        $this->param['btnLink'] = route('memorial.create');
        try {
            $keyword = $request->get('keyword');
            $getMemorial = Memorial::orderBy('tanggal', 'DESC')->orderBy('created_at', 'DESC')->onlyTrashed();

            if ($keyword) {
                $getMemorial->where('kode_memorial', 'LIKE', "%{$keyword}%")->orWhere('tipe', 'LIKE', "%{$keyword}%");
            }

            $this->param['memorial'] = $getMemorial->paginate(10);
            return view('pages.memorial.listTrash', $this->param);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        } catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
    }

    public function restoretransaksiMemorial($id)
    {
        try {
            $transaksiMemorial = Memorial::withTrashed()->findOrFail($id);
            if ($transaksiMemorial->trashed()) {
                $transaksiMemorial->restore();
                Jurnal::onlyTrashed()->where('kode_transaksi', $id)->restore(); // PERBAIKAN SAK: jurnal ikut dipulihkan
                return redirect()->route('transaksiMemorial.trash')->withStatus('Data berhasil di kembalikan.');
            }
            else
            {
                return redirect()->route('transaksiMemorial.trash')->withError('Data tidak ada dalam sampah.');
            }

        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
        catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $trashMemorial = Memorial::findOrFail($id);
            // return $trashUser;
            // if ($trashMemorial->deleted_by = auth()->user()->id) {
            //     $trashMemorial->update();
            // }
            $trashMemorial->delete();
            Jurnal::where('kode_transaksi', $id)->delete(); // PERBAIKAN SAK: jurnal ikut ke sampah (soft delete) agar tidak terhitung di laporan
            return redirect()->route('memorial.index')->withStatus('Berhasil memindahkan ke sampah');

        } catch (\Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        }
    }

    public function DetailMemorial()
    {
        $next = $_GET['biggestNo'] + 1;
        $kode_lawan = KodeAkun::select('kode_akun.kode_akun','kode_akun.nama')
                                ->get();
        return view('pages.memorial.form-detail-memorial-kas', ['hapus' => true, 'no' => $next, 'kode_lawan' => $kode_lawan,'kode_akun' => $kode_lawan]);
    }

    // report memorial
    public function reportMemorial()
    {
        try {

            $this->param['report_memorial'] = null;
            return view('pages.memorial.laporan-memorial',$this->param);
        }catch(\Exception $e){
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        }catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }
    public function getReport(Request $request)
    {
        $request->validate([
            'start' => 'required',
            'end' => 'required'
        ],[
            'required', ':atrribute harus terisi',
            'no_in' => ':attribute harus terisi'
        ]);
        // return $request;
        try {
            $this->param['report_memorial'] = Memorial::select(
                                                    'memorial.kode_memorial',
                                                    'memorial.tanggal',
                                                    'memorial.tipe',
                                                    'memorial.total',
                                                    'memorial_detail.kode_memorial',
                                                    'memorial_detail.kode',
                                                    'memorial_detail.lawan',
                                                    'memorial_detail.subtotal',
                                                    'memorial_detail.keterangan')
                                                    ->join('memorial_detail','memorial_detail.kode_memorial','memorial.kode_memorial')
                                                    // ->whereBetween('memorial.tanggal', [$request->get('start'), $request->get('end')])
                                                    ->whereBetween('memorial.tanggal',[$request->start,$request->end])
                                                    ->get();
            return view('pages.memorial.laporan-memorial',$this->param);
        }catch(\Exception $e){
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        }catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }
    public function printReport(Request $request)
    {
        $request->validate([
            'start' => 'required',
            'end' => 'required'
        ],[
            'required', ':atrribute harus terisi',
            'no_in' => ':attribute harus terisi'
        ]);
        // return $request;
        try {
            $this->param['report_memorial'] = Memorial::select(
                                                    'memorial.kode_memorial',
                                                    'memorial.tanggal',
                                                    'memorial.tipe',
                                                    'memorial.total',
                                                    'memorial_detail.kode_memorial',
                                                    'memorial_detail.kode',
                                                    'memorial_detail.lawan',
                                                    'memorial_detail.subtotal',
                                                    'memorial_detail.keterangan')
                                                    ->join('memorial_detail','memorial_detail.kode_memorial','memorial.kode_memorial')
                                                    // ->whereBetween('memorial.tanggal', [$request->get('start'), $request->get('end')])
                                                    ->whereBetween('memorial.tanggal',[$request->start,$request->end])
                                                    ->get();
            return view('pages.memorial.print-laporan-memorial',$this->param);
        }catch(\Exception $e){
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        }catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }
}
