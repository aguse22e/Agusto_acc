<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\KodeAkun;

class CustomerController extends Controller
{
    private $param;

    public function __construct()
    {
        $this->param['pageTitle'] = 'Customer';
        $this->param['pageIcon'] = 'fa-solid fa-user-tag';
        $this->param['parentMenu'] = 'customer';
        $this->param['current'] = 'Customer';
    }

    public function index(Request $request)
    {
        $this->param['btnText'] = 'Tambah Customer';
        $this->param['btnLink'] = route('customer.create');
        $this->param['btnTrashText'] = 'Lihat Sampah';
        $this->param['btnTrashLink'] = route('customer.trash');

        try {
            $keyword = $request->get('keyword');
            $getCustomer = Customer::orderBy('nama', 'ASC');

            if ($keyword) {
                $getCustomer->where('nama', 'LIKE', "%{$keyword}%");
            }

            $this->param['customer'] = $getCustomer->paginate(10);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
        catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }

        return view('pages.customer.index', $this->param);
    }

    public function create()
    {
        $this->param['btnText'] = 'List Customer';
        $this->param['btnLink'] = route('customer.index');
        $this->param['semuaAkun'] = KodeAkun::orderBy('kode_akun', 'ASC')->get();

        return view('pages.customer.create',$this->param);
    }

    private function kodeBaru()
    {
        $terakhir = Customer::withTrashed()->max('id');
        return 'CUST-' . str_pad(((int) $terakhir) + 1, 4, '0', STR_PAD_LEFT);
    }

    private function validasi(Request $request, $id = null)
    {
        return $request->validate([
            'nama' => 'required',
            'alamat' => 'required',
            'no_hp' => ['required', Rule::unique('customer', 'no_hp')->ignore($id)],
            'email' => ['nullable', 'email', Rule::unique('customer', 'email')->ignore($id)],
            'termin_hari' => 'nullable|integer|min:0',
            'piutang' => 'nullable|numeric|min:0',
        ]);
    }

    private function isiData(Request $request, Customer $data)
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
        $data->piutang = $request->piutang ?: 0;
        $data->akun_piutang = $request->akun_piutang ?: null;
        $data->catatan = $request->catatan;
        return $data;
    }
    public function store(Request $request)
    {
        $this->validasi($request);
        try {
            $addCustomer = new Customer;
            $addCustomer->kode_customer = $this->kodeBaru();
            $this->isiData($request, $addCustomer);
            $addCustomer->kategori = $request->kategori;
            $addCustomer->limit_kredit = $request->limit_kredit ?: 0;
            $addCustomer->save();
            return redirect()->route('customer.index')->withStatus('Berhasil menambahkan data');
        } catch (QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (Exception $e){
            return redirect()->back()->withError('Terjadi kesalahan.');
        }
    }
    public function show($id)
    {
        $this->param['btnText'] = 'Kembali';
        $this->param['btnLink'] = route('customer.index');
        try {
            $this->param['customer'] = Customer::findOrFail($id);
            return view('pages.customer.show', $this->param);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->withError('Data tidak ditemukan.');
        }
    }

    public function edit($id)
    {
        try {
            $this->param['btnText'] = 'Lihat Customer';
            $this->param['btnLink'] = route('customer.index');
            $this->param['data'] = Customer::findOrFail($id);
            $this->param['semuaAkun'] = KodeAkun::orderBy('kode_akun', 'ASC')->get();
            return view('pages.customer.edit',$this->param);
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
            $customer = Customer::findOrFail($id);
            $this->isiData($request, $customer);
            $customer->kategori = $request->kategori;
            $customer->limit_kredit = $request->limit_kredit ?: 0;
            $customer->save();
            return redirect()->route('customer.index')->withStatus('Berhasil memperbarui data');
        } catch (QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (Exception $e){
            return redirect()->back()->withError('Terjadi kesalahan.');
        }
    }
    public function destroy($id)
    {
        try {
            $trashCustomer = Customer::findOrFail($id);
            // return $trashCustomer;
            if ($trashCustomer->deleted_by = auth()->user()->id) {
                $trashCustomer->update();
            }
            $trashCustomer->delete();
            return redirect()->route('customer.index')->withStatus('Berhasil memindahkan ke tempat sampah');

        } catch (\Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        }
    }

    public function trashCustomer(Request $request)
    {
        $this->param['btnText'] = 'Tambah Customer';
        $this->param['btnLink'] = route('customer.create');
        $this->param['btnTrashText'] = 'Lihat Sampah';
        $this->param['btnTrashLink'] = route('customer.trash');

        try {
            $keyword = $request->get('keyword');
            $getCustomer = Customer::onlyTrashed();

            if ($keyword) {
                $getCustomer->where('nama', 'LIKE', "%{$keyword}%");
            }

            $this->param['customer'] = $getCustomer->paginate(10);
            return view('pages.customer.listTrash',$this->param);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
        catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
    }
    public function restoreCustomer($id)
    {
        try {
            $customer = Customer::withTrashed()->findOrFail($id);
            if ($customer->trashed()) {
                $customer->deleted_by = null;
                $customer->restore();
                return redirect()->route('customer.trash')->withStatus('Data berhasil di restore.');
            }
            else
            {
                return redirect()->route('customer.trash')->withError('Data tidak ada dalam tempat sampah.');
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
            $deleteCustomer = Customer::onlyTrashed()->where('id',$id);
            $deleteCustomer->forceDelete();
            return redirect()->route('customer.trash')->withStatus('Data berhasil dihapus permanen.');

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
