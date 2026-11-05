<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\KodeAkun;
use App\Models\KodeInduk;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class KodeAkunController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $param;

    public function __construct()
    {
        $this->param['pageTitle'] = 'Kode Akun';
        $this->param['pageIcon'] = 'feather icon-bookmark';
        $this->param['parentMenu'] = 'Kode Akun';
        $this->param['current'] = 'Kode Akun';
    }
    public function index(Request $request)
    {
        $this->param['btnText'] = 'Tambah Kode Akun';
        $this->param['btnLink'] = route('kode-akun.create');
        $this->param['btnTrashText'] = 'Lihat Sampah';
        $this->param['btnTrashLink'] = route('kodeAkun.trash');

        try {
            $keyword = $request->get('keyword');
            $getKodeAkun = KodeAkun::with('kodeInduk')->orderBy('kode_akun', 'ASC');

            if ($keyword) {
                $getKodeAkun->where('nama', 'LIKE', "%{$keyword}%")->orWhere('kode_akun', 'LIKE', "%{$keyword}%")->orWhere('tipe', 'LIKE', "%{$keyword}%");
            }

            $this->param['kode_akun'] = $getKodeAkun->paginate(10);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
        catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }

        return view('pages.kode-akun.index', $this->param);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->param['btnText'] = 'Lihat Kode Akun';
        $this->param['btnLink'] = route('kode-akun.index');
        $this->param['data'] = KodeInduk::all();

        return view('pages.kode-akun.create',$this->param);
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
            'induk_kode' => 'required|not_in:0',
            'kode_akun' => 'required|unique:kode_akun',
            'tipe' => 'required|not_in:0',
            'jenis_akun' => 'required|in:Kas,Bank,Lainnya',
            'nama' => 'required',
        ],[
            'required' => ':attribute harus terisi.',
            'not_in' => ':attribute harus terisi.'
        ],[
            'induk_kode' => 'Kode induk',
            'kode_akun' => 'Kode akun',
            'nama' => 'Nama akun',
        ]);
        // return $request;
        try {
            $kode_akun = $request->kode_akun;
            $addData = new KodeAkun;
            $addData->kode_akun = $kode_akun;
            $addData->induk_kode = $request->induk_kode;
            $addData->tipe = $request->tipe;
            $addData->jenis_akun = $request->jenis_akun;
            $addData->nama = str_replace('-',' ',$request->nama);
            $addData->save();
            return redirect()->route('kode-akun.index')->withStatus('Berhasil menambahkan data.');
        } catch (QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (Exception $e){
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
        //
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
            $this->param['btnText'] = 'Lihat Kode Akun';
            $this->param['btnLink'] = route('kode-akun.index');
            $this->param['data'] = KodeAkun::findOrFail($id);
            $this->param['data_induk'] = KodeInduk::all();
            return view('pages.kode-akun.edit',$this->param);
        } catch (QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (Exception $e){
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
        $kode_akun = KodeAkun::find($id);
        $isUniqueKodeAkun = $kode_akun->kode_akun == $request->kode_akun ? '' : '|unique:kode_akun';
        $isUniqueNamaAkun = $kode_akun->nama == $request->nama ? '' : '|unique:kode_akun';
        $request->validate([
            'induk_kode' => 'required|not_in:0',
            'kode_akun' => 'required'.$isUniqueKodeAkun,
            'nama' => 'required'.$isUniqueNamaAkun,
            'tipe' => 'required|not_in:0',
            'jenis_akun' => 'required|in:Kas,Bank,Lainnya',
        ],[
            'unique' => ':attribute sudah tersedia.',
            'required' => ':attribute harus terisi.',
            'not_in' => ':attribute harus terisi.'
        ],[
            'induk_kode' => 'Kode induk',
            'kode_akun' => 'Kode akun',
            'nama' => 'Nama akun',
        ]);
        try {
            $kode_akun = $request->kode_akun;
            $updateData = KodeAkun::findOrFail($id);
            $updateData->kode_akun = $kode_akun;
            $updateData->induk_kode = $request->induk_kode;
            $updateData->tipe = $request->tipe;
            $updateData->jenis_akun = $request->jenis_akun;
            $updateData->nama = str_replace('-',' ',$request->nama);
            $updateData->save();
            return redirect()->route('kode-akun.index')->withStatus('Berhasil menambahkan data.');
        } catch (QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->back()->withError('Gagal menyimpan data karena Kode Induk sudah dipakai di transaksi.');
            }
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (Exception $e){
            return $e;
            return redirect()->back()->withError('Terjadi kesalahan.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $trashKodeAkun = KodeAkun::findOrFail($id);
            // return $trashUser;
            if ($trashKodeAkun->deleted_by = auth()->user()->id) {
                $trashKodeAkun->update();
            }
            $trashKodeAkun->delete();
            return redirect()->route('kode-akun.index')->withStatus('Berhasil memindahkan ke sampah');

        } catch (\Exception $e) {
            return $e;
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (\Illuminate\Database\QueryException $e) {
            return $e;
            return redirect()->back()->withError('Terjadi kesalahan.');
        }
    }
    public function trashKodeAkun(Request $request)
    {
        $this->param['btnText'] = 'Tambah Kode Akun';
        $this->param['btnLink'] = route('kode-akun.create');
        try {
            $keyword = $request->get('keyword');
            $getKodeAkun = KodeAkun::with('kodeInduk', 'user')->onlyTrashed();
                // ->select('kode_akun.kode_akun as kode_akun','kode_akun.nama','kode_akun.saldo_awal','kode_akun.deleted_by','users.id','users.name')
                // ->join('users','kode_akun.deleted_by','users.id')->onlyTrashed();


            if ($keyword) {
                $getKodeAkun->where('nama', 'LIKE', "%{$keyword}%")->orWhere('kode_akun', 'LIKE', "%{$keyword}%");
            }

            $this->param['kode_akun'] = $getKodeAkun->paginate(10);
            return view('pages.kode-akun.listTrash',$this->param);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
        catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }

        // return view('pages.users.index', $this->param);
        // $this->param['data'] = User::onlyTrashed()->get();
    }
    public function restoreKodeAkun($id)
    {
        try {
            $kodeAkun = KodeAkun::withTrashed()->findOrFail($id);
            if ($kodeAkun->trashed()) {
                $kodeAkun->deleted_by = null;
                $kodeAkun->restore();
                return redirect()->route('kodeAkun.trash')->withStatus('Data berhasil di kembalikan.');
            }
            else
            {
                return redirect()->route('kodeAkun.trash')->withError('Data tidak ada dalam sampah.');
            }

        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
        catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }

    }
    public function hapusPermanen($id)
    {
        // return   $id;
        try {
            $deleteKodeAkun = KodeAkun::onlyTrashed()->find($id);
            $deleteKodeAkun->forceDelete();
            return redirect()->route('kodeAkun.trash')->withStatus('Data berhasil dihapus permanen.');

        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
        catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
    }
}
