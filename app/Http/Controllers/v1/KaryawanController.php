<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\JadwalKerja;
use App\Models\Karyawan;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KaryawanController extends Controller
{
    private $param;

    public function __construct()
    {
        $this->param['pageTitle'] = 'Data Karyawan';
        $this->param['pageIcon'] = 'fa-solid fa-users';
        $this->param['parentMenu'] = 'karyawan';
        $this->param['current'] = 'Karyawan';
    }

    public function index(Request $request)
    {
        $this->param['btnText'] = 'Tambah Karyawan';
        $this->param['btnLink'] = route('karyawan.create');
        $this->param['btnTrashText'] = 'Lihat Sampah';
        $this->param['btnTrashLink'] = route('karyawan.trash');

        try {
            $keyword = $request->get('keyword');
            $getKaryawan = Karyawan::orderBy('nama', 'ASC');

            if ($keyword) {
                $getKaryawan->where(function ($q) use ($keyword) {
                    $q->where('nama', 'LIKE', "%{$keyword}%")
                        ->orWhere('kode_karyawan', 'LIKE', "%{$keyword}%")
                        ->orWhere('jabatan', 'LIKE', "%{$keyword}%")
                        ->orWhere('departemen', 'LIKE', "%{$keyword}%");
                });
            }

            $this->param['karyawan'] = $getKaryawan->paginate(10);
        } catch (QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        } catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }

        return view('pages.karyawan.index', $this->param);
    }

    public function create()
    {
        $this->param['btnText'] = 'List Karyawan';
        $this->param['btnLink'] = route('karyawan.index');

        return view('pages.karyawan.create', $this->param);
    }

    private function kodeKaryawanBaru()
    {
        $terakhir = Karyawan::withTrashed()->max('id');
        return 'KRY-' . str_pad(((int) $terakhir) + 1, 4, '0', STR_PAD_LEFT);
    }

    private function validasi(Request $request, $id = null)
    {
        return $request->validate([
            'nama' => 'required',
            'tanggal_masuk' => 'required|date',
            'email' => ['nullable', 'email', Rule::unique('karyawan', 'email')->ignore($id)],
            'status_karyawan' => 'required|in:Tetap,Kontrak,Harian,Magang',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'gaji_pokok' => 'nullable|numeric|min:0',
        ]);
    }

    private function isiData(Request $request, Karyawan $karyawan)
    {
        $karyawan->nama = $request->nama;
        $karyawan->jabatan = $request->jabatan;
        $karyawan->departemen = $request->departemen;
        $karyawan->tempat_lahir = $request->tempat_lahir;
        $karyawan->tanggal_lahir = $request->tanggal_lahir;
        $karyawan->jenis_kelamin = $request->jenis_kelamin;
        $karyawan->alamat = $request->alamat;
        $karyawan->no_hp = $request->no_hp;
        $karyawan->email = $request->email;
        $karyawan->tanggal_masuk = $request->tanggal_masuk;
        $karyawan->status_karyawan = $request->status_karyawan;
        $karyawan->aktif = $request->has('aktif') ? 1 : 0;
        $karyawan->npwp = $request->npwp;
        $karyawan->nama_bank = $request->nama_bank;
        $karyawan->no_rekening = $request->no_rekening;
        $karyawan->gaji_pokok = $request->gaji_pokok ?: 0;
        return $karyawan;
    }

    public function store(Request $request)
    {
        $this->validasi($request);
        try {
            $addKaryawan = new Karyawan;
            $addKaryawan->kode_karyawan = $this->kodeKaryawanBaru();
            $this->isiData($request, $addKaryawan)->save();
            return redirect()->route('karyawan.index')->withStatus('Berhasil menambahkan data karyawan ' . $addKaryawan->kode_karyawan);
        } catch (QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        }
    }

    public function show($id)
    {
        $this->param['btnText'] = 'Kembali';
        $this->param['btnLink'] = route('karyawan.index');
        try {
            $this->param['karyawan'] = Karyawan::findOrFail($id);
            $this->param['jadwalMendatang'] = JadwalKerja::with('shift')
                ->where('karyawan_id', $id)
                ->where('tanggal', '>=', date('Y-m-d'))
                ->orderBy('tanggal', 'ASC')
                ->limit(14)
                ->get();
            return view('pages.karyawan.show', $this->param);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->withError('Data tidak ditemukan.');
        }
    }

    public function edit($id)
    {
        $this->param['btnText'] = 'List Karyawan';
        $this->param['btnLink'] = route('karyawan.index');
        try {
            $this->param['karyawan'] = Karyawan::findOrFail($id);
            return view('pages.karyawan.edit', $this->param);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->withError('Data tidak ditemukan.');
        }
    }

    public function update(Request $request, $id)
    {
        $this->validasi($request, $id);
        try {
            $karyawan = Karyawan::findOrFail($id);
            $this->isiData($request, $karyawan)->save();
            return redirect()->route('karyawan.index')->withStatus('Berhasil memperbarui data');
        } catch (QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        }
    }

    public function destroy($id)
    {
        try {
            $karyawan = Karyawan::findOrFail($id);
            $karyawan->delete();
            return redirect()->route('karyawan.index')->withStatus('Data berhasil dipindahkan ke tempat sampah.');
        } catch (QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        } catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
    }

    public function trashKaryawan(Request $request)
    {
        $this->param['btnText'] = 'List Karyawan';
        $this->param['btnLink'] = route('karyawan.index');
        try {
            $keyword = $request->get('keyword');
            $getKaryawan = Karyawan::onlyTrashed()->orderBy('nama', 'ASC');

            if ($keyword) {
                $getKaryawan->where('nama', 'LIKE', "%{$keyword}%");
            }

            $this->param['karyawan'] = $getKaryawan->paginate(10);
            return view('pages.karyawan.listTrash', $this->param);
        } catch (QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        } catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
    }

    public function restoreKaryawan($id)
    {
        try {
            $karyawan = Karyawan::withTrashed()->findOrFail($id);
            if ($karyawan->trashed()) {
                $karyawan->restore();
                return redirect()->route('karyawan.trash')->withStatus('Data berhasil di restore.');
            } else {
                return redirect()->route('karyawan.trash')->withError('Data tidak ada dalam tempat sampah.');
            }
        } catch (QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        } catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
    }

    public function hapusPermanen($id)
    {
        try {
            $karyawan = Karyawan::withTrashed()->findOrFail($id);
            if ($karyawan->trashed()) {
                if (JadwalKerja::withTrashed()->where('karyawan_id', $id)->exists()) {
                    return redirect()->route('karyawan.trash')->withError('Tidak dapat dihapus permanen: karyawan ini memiliki riwayat jadwal kerja.');
                }
                $karyawan->forceDelete();
                return redirect()->route('karyawan.trash')->withStatus('Data berhasil dihapus permanen.');
            } else {
                return redirect()->route('karyawan.trash')->withError('Data tidak ada dalam tempat sampah.');
            }
        } catch (QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        } catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
    }
}
