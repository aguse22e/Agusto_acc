<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\KodeInduk;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class KodeIndukController extends Controller
{
    private $param;

    public function __construct()
    {
        $this->param['pageTitle'] = 'Kode Induk';
        $this->param['pageIcon'] = 'feather icon-bookmark';
        $this->param['parentMenu'] = 'Kode Induk';
        $this->param['current'] = 'Kode Induk';
    }

    public function index(Request $request)
    {
        $this->param['btnText'] = 'Tambah Kode Induk';
        $this->param['btnLink'] = route('kode-induk.create');
        try {
            $keyword = $request->get('keyword');
            $getKodeInduk = KodeInduk::orderBy('kode_induk', 'ASC');

            if ($keyword) {
                $getKodeInduk->where('nama', 'LIKE', "%{$keyword}%")->orWhere('kode_induk', 'LIKE', "%{$keyword}%");
            }

            $this->param['kode_induk'] = $getKodeInduk->paginate(10);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
        catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }

        return view('pages.kode-induk.index', $this->param);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->param['btnText'] = 'Lihat Kode Induk';
        $this->param['btnLink'] = route('kode-induk.index');
        return view('pages.kode-induk.create',$this->param);
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
            'kode_induk' => 'required|unique:kode_induk',
            'nama' => 'required',
        ],[
            'required' => ':attribute harus terisi.',
            'not_in' => ':attribute harus terisi.'
        ],[
            'kode_induk' => 'Kode induk',
            'nama' => 'Nama induk'
        ]);
        try {
            $addData = new KodeInduk;
            $addData->kode_induk = $request->kode_induk;
            $addData->nama = $request->nama;
            $addData->save();
            return redirect()->route('kode-induk.index')->withStatus('Berhasil menambahkan data.');
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
            $this->param['btnText'] = 'Lihat Kode Induk';
            $this->param['btnLink'] = route('kode-induk.index');
            $this->param['data'] = KodeInduk::find($id);
            return view('pages.kode-induk.edit',$this->param);
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
        $kodeInduk = KodeInduk::find($id);
        $isUniqueKode = $kodeInduk->kode_induk == $request->kode_induk ? '' : '|unique:kode_induk';
        $isUniqueNama = $kodeInduk->nama == $request->nama ? '' : '|unique:kode_induk';
        $request->validate([
            'kode_induk' => 'required'.$isUniqueKode,
            'nama' => 'required'.$isUniqueNama,
        ],[
            'required' => ':attribute harus terisi.',
            'not_in' => ':attribute harus terisi.'
        ],[
            'kode_induk' => 'Kode induk',
            'nama' => 'Nama induk'
        ]);
        try {
            $updateData = KodeInduk::find($id);
            $updateData->kode_induk = $request->kode_induk;
            $updateData->nama = $request->nama;
            $updateData->save();
            return redirect()->route('kode-induk.index')->withStatus('Berhasil memperbarui data.');
        } catch (QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (Exception $e){
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
            $trashKodeInduk = KodeInduk::findOrFail($id);
            // return $trashUser;
            if ($trashKodeInduk->deleted_by = auth()->user()->id) {
                $trashKodeInduk->update();
            }
            $trashKodeInduk->delete();
            return redirect()->route('kode-induk.index')->withStatus('Berhasil memindahkan ke sampah');

        } catch (\Exception $e) {
            return $e;
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (\Illuminate\Database\QueryException $e) {
            return $e;
            return redirect()->back()->withError('Terjadi kesalahan.');
        }
    }
    public function trashKodeInduk(Request $request)
    {
        $this->param['btnText'] = 'Tambah Kode Induk';
        $this->param['btnLink'] = route('kode-induk.create');
        try {
            $keyword = $request->get('keyword');
            $getKodeInduk = KodeInduk::select('kode_induk.kode_induk as kode_induk','kode_induk.nama','kode_induk.deleted_by','users.id','users.name')
                                        ->join('users','kode_induk.deleted_by','users.id')->onlyTrashed();

            if ($keyword) {
                $getKodeInduk->where('nama', 'LIKE', "%{$keyword}%")->orWhere('kode_induk', 'LIKE', "%{$keyword}%");
            }

            $this->param['kode_induk'] = $getKodeInduk->paginate(10);
            return view('pages.kode-induk.listTrash',$this->param);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
        catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }

        // return view('pages.users.index', $this->param);
        // $this->param['data'] = User::onlyTrashed()->get();
    }
    public function restoreKodeInduk($id)
    {
        try {
            $kodeInduk = KodeInduk::withTrashed()->findOrFail($id);
            if ($kodeInduk->trashed()) {
                $kodeInduk->deleted_by = null;
                $kodeInduk->restore();
                return redirect()->route('kodeInduk.trash')->withStatus('Data berhasil di kembalikan.');
            }
            else
            {
                return redirect()->route('kodeInduk.trash')->withError('Data tidak ada dalam sampah.');
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
            $deleteKodeInduk = KodeInduk::onlyTrashed()->find($id);
            $deleteKodeInduk->forceDelete();
            return redirect()->route('kodeInduk.trash')->withStatus('Data berhasil dihapus permanen.');

        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
        catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
    }

}
