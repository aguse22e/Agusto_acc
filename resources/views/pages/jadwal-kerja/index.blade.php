@extends('layouts.template')

@section('page-header')
    @include('components.page-header', [
    'pageTitle' => $pageTitle,
    'pageSubtitle' => '',
    'pageIcon' => $pageIcon,
    'parentMenu' => $parentMenu,
    'current' => $current
    ])
@endsection

@section('content')

    @include('components.notification')

    @include('components.button-add', ['btnText' => $btnText, 'btnLink' => $btnLink])
    <a href="{{ route('jadwal-kerja.generate') }}" class="btn btn-success btn-sm mb-3"><i class="feather icon-calendar"></i> Generate Jadwal</a>
    <a href="{{ route('jadwal-kerja.trash') }}" class="btn btn-secondary btn-sm mb-3"><i class="feather icon-trash"></i> Tempat Sampah</a>

    <div class="card">
        <div class="card-header">
            <h5>Matriks Jadwal — {{ date('F Y', mktime(0, 0, 0, $bulan, 1, $tahun)) }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ url('jadwal-kerja') }}" method="GET">
                <div class="row">
                    <div class="col-md-2 mb-3">
                        <label>Bulan</label>
                        <select name="bulan" class="form-control">
                            @for ($b = 1; $b <= 12; $b++)
                                <option value="{{ $b }}" {{ $bulan == $b ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $b, 1)) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label>Tahun</label>
                        <input type="number" name="tahun" class="form-control" value="{{ $tahun }}" min="2000" max="2100">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Karyawan</label>
                        <select name="karyawan_id" class="form-control">
                            <option value="">Semua Karyawan Aktif</option>
                            @foreach ($semuaKaryawan as $k)
                                <option value="{{ $k->id }}" {{ $karyawanId == $k->id ? 'selected' : '' }}>{{ $k->kode_karyawan . ' ~ ' . $k->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mt-4">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-block table-border-style">
            <div class="table-responsive">
                <table class="table table-bordered table-sm" style="font-size: 11px;">
                    <thead>
                        <tr class="table-primary">
                            <th>Karyawan</th>
                            @for ($d = 1; $d <= $jumlahHari; $d++)
                                <th class="text-center">{{ $d }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $warnaStatus = ['Terjadwal' => 'secondary', 'Hadir' => 'success', 'Izin' => 'warning', 'Sakit' => 'info', 'Cuti' => 'primary', 'Alpa' => 'danger', 'Libur' => 'light'];
                        @endphp
                        @foreach ($karyawanList as $k)
                            <tr>
                                <td style="white-space: nowrap;">{{ $k->nama }}</td>
                                @for ($d = 1; $d <= $jumlahHari; $d++)
                                    @php
                                        $tgl = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
                                        $entri = isset($peta[$k->id][$tgl]) ? $peta[$k->id][$tgl] : null;
                                    @endphp
                                    <td class="text-center">
                                        @if ($entri)
                                            <a href="{{ route('jadwal-kerja.edit', $entri->id) }}" title="{{ $entri->shift ? $entri->shift->nama_shift : 'Tanpa shift' }} — {{ $entri->status }}{{ $entri->keterangan ? ' (' . $entri->keterangan . ')' : '' }}">
                                                <span class="badge badge-{{ $warnaStatus[$entri->status] }}">{{ $entri->status == 'Libur' ? 'L' : ($entri->shift ? strtoupper(substr($entri->shift->nama_shift, 0, 1)) : '•') }}</span>
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endfor
                            </tr>
                        @endforeach
                        @if ($karyawanList->isEmpty())
                            <tr><td colspan="{{ $jumlahHari + 1 }}" class="text-center">Tidak ada karyawan aktif.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <p class="text-muted">Huruf pada sel adalah inisial shift (P/S/M), L = Libur. Warna mengikuti status: abu = Terjadwal, hijau = Hadir, kuning = Izin, biru muda = Sakit, biru = Cuti, merah = Alpa. Klik sel untuk mengubah entri.</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5>Rekap Kehadiran — {{ date('F Y', mktime(0, 0, 0, $bulan, 1, $tahun)) }}</h5>
        </div>
        <div class="card-block table-border-style">
            <div class="table-responsive">
                <table class="table table-styling">
                    <thead>
                        <tr class="table-primary">
                            <th>Karyawan</th>
                            @foreach ($daftarStatus as $st)
                                <th class="text-center">{{ $st }}</th>
                            @endforeach
                            <th class="text-center">Total Entri</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($karyawanList as $k)
                            <tr>
                                <td>{{ $k->kode_karyawan . ' ~ ' . $k->nama }}</td>
                                @foreach ($daftarStatus as $st)
                                    <td class="text-center">{{ $rekap[$k->id][$st] }}</td>
                                @endforeach
                                <td class="text-center"><b>{{ $rekap[$k->id]['total'] }}</b></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="text-muted">Rekap ini menjadi dasar perhitungan hari kerja efektif untuk penggajian (jurnal beban gaji dibuat lewat Memorial).</p>
        </div>
    </div>
@endsection
