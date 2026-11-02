<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\KodeAkun;

class SupplierController extends Controller
{
    private $param;

    public function __construct()
    {
        $this->param['pageTitle'] = 'Supplier';
        $this->param['pageIcon'] = 'fa-solid fa-people-carry-box';
        $this->param['parentMenu'] = 'supplier';
        $this->param['current'] = 'Supplier';
    }

    public function index(Request $request)
    {
        $this->param['btnText'] = 'Tambah Supplier';
        $this->param['btnLink'] = route('supplier.create');
        $this->param['btnTrashText'] = 'Lihat Sampah';
        $this->param['btnTrashLink'] = route('supplier.trash');

        try {
            $keyword = $request->get('keyword');
            $getSupplier = Supplier::orderBy('nama', 'ASC');

            if ($keyword) {
                $getSupplier->where('nama', 'LIKE', "%{$keyword}%");
            }

            $this->param['supplier'] = $getSupplier->paginate(10);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
        catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }

        return view('pages.supplier.index', $this->param);
    }

    public function create()
    {
        $this->param['btnText'] = 'List Supplier';
        $this->param['btnLink'] = route('supplier.index');
        $this->param['semuaAkun'] = KodeAkun::orderBy('kode_akun', 'ASC')->get();

        return view('pages.supplier.create',$this->param);
    }

    private function kodeBaru()
    {
        $terakhir = Supplier::withTrashed()->max('id');
        return 'SUPP-' . str_pad(((int) $terakhir) + 1, 4, '0', STR_PAD_LEFT);
    }

    private function validasi(Request $request, $id = null)
    {
        return $request->validate([
            'nama' => 'required',
            'alamat' => 'required',
            'no_hp' => ['required', Rule::unique('supplier', 'no_hp')->ignore($id)],
            'email' => ['nullable', 'email', Rule::unique('supplier', 'email')->ignore($id)],
            'termin_hari' => 'nullable|integer|min:0',
            'hutang' => 'nullable|numeric|min:0',
        ]);
    }

    private function isiData(Request $request, Supplier $data)
    {
        $data->nama = $request->nama;
        $data->alamat = $request->alamat;
        $data->no_hp = $request->no_hp;
        $data->kontak_person = $request->kontak_person;
        $data->telepon = $request->telepon;
        $data->email = $request->email;
        $data->kota = $request->kota;
        $data->provinsi = $request->provinsi;
        $data->kode_pos = $request->kode_pos;
        $data->npwp = $request->npwp;
        $data->termin_hari = $request->termin_hari ?: 0;
        $data->hutang = $request->hutang ?: 0;
        $data->akun_hutang = $request->akun_hutang ?: null;
        $data->catatan = $request->catatan;
        return $data;
    }
    public function store(Request $request)
    {
        $this->validasi($request);
        try {
            $addSupplier = new Supplier;
            $addSupplier->kode_supplier = $this->kodeBaru();
            $this->isiData($request, $addSupplier);
            $addSupplier->nama_bank = $request->nama_bank;
            $addSupplier->no_rekening = $request->no_rekening;
            $addSupplier->save();
            return redirect()->route('supplier.index')->withStatus('Berhasil menambahkan data');
        } catch (QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (Exception $e){
            return redirect()->back()->withError('Terjadi kesalahan.');
        }
    }
    public function show($id)
    {
        $this->param['btnText'] = 'Kembali';
        $this->param['btnLink'] = route('supplier.index');
        try {
            $this->param['supplier'] = Supplier::findOrFail($id);
            return view('pages.supplier.show', $this->param);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->withError('Data tidak ditemukan.');
        }
    }

    public function edit($id)
    {
        try {
            $this->param['btnText'] = 'Lihat Supplier';
            $this->param['btnLink'] = route('supplier.index');
            $this->param['data'] = Supplier::findOrFail($id);
            $this->param['semuaAkun'] = KodeAkun::orderBy('kode_akun', 'ASC')->get();
            return view('pages.supplier.edit',$this->param);
        } catch (QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (Exception $e){
            return redirect()->back()->withError('Terjadi kesalahan.');

        }
    }

    public function update(Request $request, $id)
    {
        $this->validasi($request, $id);
        try {
            $supplier = Supplier::findOrFail($id);
            $this->isiData($request, $supplier);
            $supplier->nama_bank = $request->nama_bank;
            $supplier->no_rekening = $request->no_rekening;
            $supplier->save();
            return redirect()->route('supplier.index')->withStatus('Berhasil memperbarui data');
        } catch (QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (Exception $e){
            return redirect()->back()->withError('Terjadi kesalahan.');
        }
    }
    public function destroy($id)
    {
        try {
            $trashSupplier = Supplier::findOrFail($id);
            // return $trashSupplier;
            if ($trashSupplier->deleted_by = auth()->user()->id) {
                $trashSupplier->update();
            }
            $trashSupplier->delete();
            return redirect()->route('supplier.index')->withStatus('Berhasil memindahkan ke tempat sampah');

        } catch (\Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        }
    }

    public function trashSupplier(Request $request)
    {
        $this->param['btnText'] = 'Tambah Supplier';
        $this->param['btnLink'] = route('supplier.create');
        $this->param['btnTrashText'] = 'Lihat Sampah';
        $this->param['btnTrashLink'] = route('supplier.trash');

        try {
            $keyword = $request->get('keyword');
            $getSupplier = Supplier::onlyTrashed();

            if ($keyword) {
                $getSupplier->where('nama', 'LIKE', "%{$keyword}%");
            }

            $this->param['supplier'] = $getSupplier->paginate(10);
            return view('pages.supplier.listTrash',$this->param);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
        catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
    }
    public function restoreSupplier($id)
    {
        try {
            $supplier = Supplier::withTrashed()->findOrFail($id);
            if ($supplier->trashed()) {
                $supplier->deleted_by = null;
                $supplier->restore();
                return redirect()->route('supplier.trash')->withStatus('Data berhasil di restore.');
            }
            else
            {
                return redirect()->route('supplier.trash')->withError('Data tidak ada dalam tempat sampah.');
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
        try {
            $deleteSupplier = Supplier::onlyTrashed()->where('id',$id);
            $deleteSupplier->forceDelete();
            return redirect()->route('supplier.trash')->withStatus('Data berhasil dihapus permanen.');

        } catch (\Illuminate\Database\QueryException $e) {
            return $e;
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
        catch (Exception $e) {
            return $e;
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
    }
}
