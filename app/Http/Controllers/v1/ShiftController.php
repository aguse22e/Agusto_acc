<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\JadwalKerja;
use App\Models\Shift;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShiftController extends Controller
{
    private $param;

    public function __construct()
    {
        $this->param['pageTitle'] = 'Master Shift';
        $this->param['pageIcon'] = 'fa-solid fa-clock';
        $this->param['parentMenu'] = 'shift';
        $this->param['current'] = 'Shift';
    }

    public function index(Request $request)
    {
        $this->param['btnText'] = 'Tambah Shift';
        $this->param['btnLink'] = route('shift.create');
        $this->param['btnTrashText'] = 'Lihat Sampah';
        $this->param['btnTrashLink'] = route('shift.trash');

        try {
            $keyword = $request->get('keyword');
            $getShift = Shift::orderBy('jam_masuk', 'ASC');

            if ($keyword) {
                $getShift->where('nama_shift', 'LIKE', "%{$keyword}%");
            }

            $this->param['shift'] = $getShift->paginate(10);
        } catch (QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        } catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }

        return view('pages.shift.index', $this->param);
    }

    public function create()
    {
        $this->param['btnText'] = 'List Shift';
        $this->param['btnLink'] = route('shift.index');

        return view('pages.shift.create', $this->param);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_shift' => 'required|unique:shift,nama_shift',
            'jam_masuk' => 'required',
            'jam_keluar' => 'required',
        ]);
        try {
            $addShift = new Shift;
            $addShift->nama_shift = $request->nama_shift;
            $addShift->jam_masuk = $request->jam_masuk;
            $addShift->jam_keluar = $request->jam_keluar;
            $addShift->keterangan = $request->keterangan;
            $addShift->save();
            return redirect()->route('shift.index')->withStatus('Berhasil menambahkan data');
        } catch (QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        }
    }

    public function show($id)
    {
        $this->param['btnText'] = 'Kembali';
        $this->param['btnLink'] = route('shift.index');
        try {
            $this->param['shift'] = Shift::findOrFail($id);
            return view('pages.shift.show', $this->param);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->withError('Data tidak ditemukan.');
        }
    }

    public function edit($id)
    {
        $this->param['btnText'] = 'List Shift';
        $this->param['btnLink'] = route('shift.index');
        try {
            $this->param['shift'] = Shift::findOrFail($id);
            return view('pages.shift.edit', $this->param);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->withError('Data tidak ditemukan.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_shift' => ['required', Rule::unique('shift', 'nama_shift')->ignore($id)],
            'jam_masuk' => 'required',
            'jam_keluar' => 'required',
        ]);
        try {
            $shift = Shift::findOrFail($id);
            $shift->nama_shift = $request->nama_shift;
            $shift->jam_masuk = $request->jam_masuk;
            $shift->jam_keluar = $request->jam_keluar;
            $shift->keterangan = $request->keterangan;
            $shift->save();
            return redirect()->route('shift.index')->withStatus('Berhasil memperbarui data');
        } catch (QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        }
    }

    public function destroy($id)
    {
        try {
            $shift = Shift::findOrFail($id);
            $shift->delete();
            return redirect()->route('shift.index')->withStatus('Data berhasil dipindahkan ke tempat sampah.');
        } catch (QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        } catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
    }

    public function trashShift(Request $request)
    {
        $this->param['btnText'] = 'List Shift';
        $this->param['btnLink'] = route('shift.index');
        try {
            $keyword = $request->get('keyword');
            $getShift = Shift::onlyTrashed()->orderBy('jam_masuk', 'ASC');

            if ($keyword) {
                $getShift->where('nama_shift', 'LIKE', "%{$keyword}%");
            }

            $this->param['shift'] = $getShift->paginate(10);
            return view('pages.shift.listTrash', $this->param);
        } catch (QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        } catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
    }

    public function restoreShift($id)
    {
        try {
            $shift = Shift::withTrashed()->findOrFail($id);
            if ($shift->trashed()) {
                $shift->restore();
                return redirect()->route('shift.trash')->withStatus('Data berhasil di restore.');
            } else {
                return redirect()->route('shift.trash')->withError('Data tidak ada dalam tempat sampah.');
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
            $shift = Shift::withTrashed()->findOrFail($id);
            if ($shift->trashed()) {
                if (JadwalKerja::withTrashed()->where('shift_id', $id)->exists()) {
                    return redirect()->route('shift.trash')->withError('Tidak dapat dihapus permanen: shift ini dipakai di jadwal kerja.');
                }
                $shift->forceDelete();
                return redirect()->route('shift.trash')->withStatus('Data berhasil dihapus permanen.');
            } else {
                return redirect()->route('shift.trash')->withError('Data tidak ada dalam tempat sampah.');
            }
        } catch (QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        } catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
    }
}
