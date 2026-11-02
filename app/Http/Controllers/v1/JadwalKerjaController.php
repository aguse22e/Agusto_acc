<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\JadwalKerja;
use App\Models\Karyawan;
use App\Models\Shift;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class JadwalKerjaController extends Controller
{
    private $param;
    private $daftarStatus = ['Terjadwal', 'Hadir', 'Izin', 'Sakit', 'Cuti', 'Alpa', 'Libur'];

    public function __construct()
    {
        $this->param['pageTitle'] = 'Jadwal Kerja';
        $this->param['pageIcon'] = 'fa-solid fa-calendar-days';
        $this->param['parentMenu'] = 'jadwal-kerja';
        $this->param['current'] = 'Jadwal Kerja';
    }

    /**
     * MODUL BARU — Jadwal Kerja: matriks bulanan karyawan x tanggal,
     * rekap kehadiran per karyawan, dan generate jadwal massal.
     * Satu karyawan hanya boleh punya satu entri per tanggal (dicek di
     * store/update dan dijamin unique index di database).
     */
    public function index(Request $request)
    {
        $this->param['btnText'] = 'Tambah Jadwal';
        $this->param['btnLink'] = route('jadwal-kerja.create');
        $this->param['btnTrashText'] = 'Lihat Sampah';
        $this->param['btnTrashLink'] = route('jadwal-kerja.trash');

        try {
            $bulan = (int) $request->get('bulan', date('m'));
            $tahun = (int) $request->get('tahun', date('Y'));
            $karyawanId = $request->get('karyawan_id');

            $queryKaryawan = Karyawan::where('aktif', 1)->orderBy('nama', 'ASC');
            if ($karyawanId) {
                $queryKaryawan->where('id', $karyawanId);
            }
            $karyawanList = $queryKaryawan->get();

            $entri = JadwalKerja::with('shift')
                ->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->whereIn('karyawan_id', $karyawanList->pluck('id'))
                ->get();

            $peta = [];
            $rekap = [];
            foreach ($karyawanList as $k) {
                $rekap[$k->id] = array_fill_keys($this->daftarStatus, 0);
                $rekap[$k->id]['total'] = 0;
            }
            foreach ($entri as $e) {
                $peta[$e->karyawan_id][$e->tanggal->format('Y-m-d')] = $e;
                if (isset($rekap[$e->karyawan_id])) {
                    $rekap[$e->karyawan_id][$e->status]++;
                    $rekap[$e->karyawan_id]['total']++;
                }
            }

            $this->param['bulan'] = $bulan;
            $this->param['tahun'] = $tahun;
            $this->param['karyawanId'] = $karyawanId;
            $this->param['jumlahHari'] = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
            $this->param['karyawanList'] = $karyawanList;
            $this->param['semuaKaryawan'] = Karyawan::orderBy('nama', 'ASC')->get();
            $this->param['peta'] = $peta;
            $this->param['rekap'] = $rekap;
            $this->param['daftarStatus'] = $this->daftarStatus;
        } catch (QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        } catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }

        return view('pages.jadwal-kerja.index', $this->param);
    }

    public function create()
    {
        $this->param['btnText'] = 'Jadwal Kerja';
        $this->param['btnLink'] = route('jadwal-kerja.index');
        $this->param['semuaKaryawan'] = Karyawan::where('aktif', 1)->orderBy('nama', 'ASC')->get();
        $this->param['semuaShift'] = Shift::orderBy('jam_masuk', 'ASC')->get();
        $this->param['daftarStatus'] = $this->daftarStatus;

        return view('pages.jadwal-kerja.create', $this->param);
    }

    private function cekDuplikat($karyawanId, $tanggal, $abaikanId = null)
    {
        $q = JadwalKerja::where('karyawan_id', $karyawanId)->where('tanggal', $tanggal);
        if ($abaikanId) {
            $q->where('id', '!=', $abaikanId);
        }
        return $q->exists();
    }

    public function store(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
            'tanggal' => 'required|date',
            'shift_id' => 'nullable|exists:shift,id',
            'status' => 'required|in:' . implode(',', $this->daftarStatus),
        ]);
        if ($this->cekDuplikat($request->karyawan_id, $request->tanggal)) {
            return back()->withError('Jadwal untuk karyawan pada tanggal tersebut sudah ada.')->withInput();
        }
        try {
            $add = new JadwalKerja;
            $add->karyawan_id = $request->karyawan_id;
            $add->tanggal = $request->tanggal;
            $add->shift_id = $request->shift_id ?: null;
            $add->status = $request->status;
            $add->keterangan = $request->keterangan;
            $add->save();
            return redirect()->route('jadwal-kerja.index', ['bulan' => date('m', strtotime($request->tanggal)), 'tahun' => date('Y', strtotime($request->tanggal))])->withStatus('Berhasil menambahkan jadwal');
        } catch (QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        }
    }

    public function edit($id)
    {
        $this->param['btnText'] = 'Jadwal Kerja';
        $this->param['btnLink'] = route('jadwal-kerja.index');
        try {
            $this->param['jadwal'] = JadwalKerja::with('karyawan')->findOrFail($id);
            $this->param['semuaKaryawan'] = Karyawan::orderBy('nama', 'ASC')->get();
            $this->param['semuaShift'] = Shift::orderBy('jam_masuk', 'ASC')->get();
            $this->param['daftarStatus'] = $this->daftarStatus;
            return view('pages.jadwal-kerja.edit', $this->param);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->withError('Data tidak ditemukan.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
            'tanggal' => 'required|date',
            'shift_id' => 'nullable|exists:shift,id',
            'status' => 'required|in:' . implode(',', $this->daftarStatus),
        ]);
        if ($this->cekDuplikat($request->karyawan_id, $request->tanggal, $id)) {
            return back()->withError('Jadwal untuk karyawan pada tanggal tersebut sudah ada.')->withInput();
        }
        try {
            $jadwal = JadwalKerja::findOrFail($id);
            $jadwal->karyawan_id = $request->karyawan_id;
            $jadwal->tanggal = $request->tanggal;
            $jadwal->shift_id = $request->shift_id ?: null;
            $jadwal->status = $request->status;
            $jadwal->keterangan = $request->keterangan;
            $jadwal->save();
            return redirect()->route('jadwal-kerja.index', ['bulan' => date('m', strtotime($request->tanggal)), 'tahun' => date('Y', strtotime($request->tanggal))])->withStatus('Berhasil memperbarui jadwal');
        } catch (QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        }
    }

    public function destroy($id)
    {
        try {
            $jadwal = JadwalKerja::findOrFail($id);
            $jadwal->delete();
            return back()->withStatus('Jadwal berhasil dipindahkan ke tempat sampah.');
        } catch (QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        } catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
    }

    public function trashJadwal(Request $request)
    {
        $this->param['btnText'] = 'Jadwal Kerja';
        $this->param['btnLink'] = route('jadwal-kerja.index');
        try {
            $this->param['jadwal'] = JadwalKerja::onlyTrashed()->with(['karyawan', 'shift'])->orderBy('tanggal', 'DESC')->paginate(10);
            return view('pages.jadwal-kerja.listTrash', $this->param);
        } catch (QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        } catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
    }

    public function restoreJadwal($id)
    {
        try {
            $jadwal = JadwalKerja::withTrashed()->findOrFail($id);
            if ($jadwal->trashed()) {
                if ($this->cekDuplikat($jadwal->karyawan_id, $jadwal->tanggal->format('Y-m-d'))) {
                    return redirect()->route('jadwal-kerja.trash')->withError('Tidak dapat di-restore: sudah ada jadwal aktif untuk karyawan pada tanggal tersebut.');
                }
                $jadwal->restore();
                return redirect()->route('jadwal-kerja.trash')->withStatus('Jadwal berhasil di restore.');
            } else {
                return redirect()->route('jadwal-kerja.trash')->withError('Data tidak ada dalam tempat sampah.');
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
            $jadwal = JadwalKerja::withTrashed()->findOrFail($id);
            if ($jadwal->trashed()) {
                $jadwal->forceDelete();
                return redirect()->route('jadwal-kerja.trash')->withStatus('Jadwal berhasil dihapus permanen.');
            } else {
                return redirect()->route('jadwal-kerja.trash')->withError('Data tidak ada dalam tempat sampah.');
            }
        } catch (QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        } catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
    }

    public function generate()
    {
        $this->param['btnText'] = 'Jadwal Kerja';
        $this->param['btnLink'] = route('jadwal-kerja.index');
        $this->param['semuaKaryawan'] = Karyawan::where('aktif', 1)->orderBy('nama', 'ASC')->get();
        $this->param['semuaShift'] = Shift::orderBy('jam_masuk', 'ASC')->get();

        return view('pages.jadwal-kerja.generate', $this->param);
    }

    /**
     * Generate jadwal massal: untuk setiap karyawan aktif terpilih dan
     * setiap tanggal dalam rentang yang cocok dengan pola hari, buat entri
     * Terjadwal dengan shift yang dipilih. Tanggal yang sudah memiliki
     * entri tidak ditimpa (dilewati dan dilaporkan jumlahnya).
     */
    public function prosesGenerate(Request $request)
    {
        $request->validate([
            'karyawan_pilihan' => 'required',
            'shift_id' => 'required|exists:shift,id',
            'tanggal_dari' => 'required|date',
            'tanggal_sampai' => 'required|date|after_or_equal:tanggal_dari',
            'pola' => 'required|in:semua,senin-jumat,senin-sabtu',
        ]);

        try {
            if ($request->karyawan_pilihan == 'semua') {
                $daftarKaryawan = Karyawan::where('aktif', 1)->get();
            } else {
                $daftarKaryawan = Karyawan::where('aktif', 1)->where('id', $request->karyawan_pilihan)->get();
            }
            if ($daftarKaryawan->isEmpty()) {
                return back()->withError('Tidak ada karyawan aktif yang dipilih.');
            }

            $dibuat = 0;
            $dilewati = 0;
            $periode = new \DatePeriod(
                new \DateTime($request->tanggal_dari),
                new \DateInterval('P1D'),
                (new \DateTime($request->tanggal_sampai))->modify('+1 day')
            );
            foreach ($periode as $tanggal) {
                $hari = (int) $tanggal->format('N'); // 1=Senin .. 7=Minggu
                if ($request->pola == 'senin-jumat' && $hari >= 6) {
                    continue;
                }
                if ($request->pola == 'senin-sabtu' && $hari == 7) {
                    continue;
                }
                $tgl = $tanggal->format('Y-m-d');
                foreach ($daftarKaryawan as $k) {
                    if ($this->cekDuplikat($k->id, $tgl)) {
                        $dilewati++;
                        continue;
                    }
                    $add = new JadwalKerja;
                    $add->karyawan_id = $k->id;
                    $add->tanggal = $tgl;
                    $add->shift_id = $request->shift_id;
                    $add->status = 'Terjadwal';
                    $add->save();
                    $dibuat++;
                }
            }

            return redirect()->route('jadwal-kerja.index', ['bulan' => date('m', strtotime($request->tanggal_dari)), 'tahun' => date('Y', strtotime($request->tanggal_dari))])
                ->withStatus("Generate selesai: {$dibuat} jadwal dibuat, {$dilewati} dilewati karena sudah ada.");
        } catch (QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        } catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
    }
}
