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

    <div class="card">
        <div class="card-block">
            <h4 class="sub-title">Detail Karyawan</h4>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Kode</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $karyawan->kode_karyawan }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nama</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $karyawan->nama }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Jabatan / Departemen</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $karyawan->jabatan }} / {{ $karyawan->departemen }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Tanggal Masuk</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ date('d-m-Y', strtotime($karyawan->tanggal_masuk)) }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Status / Aktif</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $karyawan->status_karyawan }} / {{ $karyawan->aktif ? 'Aktif' : 'Nonaktif' }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">No HP / Email</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $karyawan->no_hp }} / {{ $karyawan->email }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Gaji Pokok</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="Rp. {{ number_format($karyawan->gaji_pokok, 2, ',', '.') }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Rekening Gaji</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="{{ $karyawan->nama_bank }} {{ $karyawan->no_rekening }}" readonly>
                </div>
            </div>

            <h4 class="sub-title mt-4">Jadwal Mendatang</h4>
            <div class="table-responsive">
                <table class="table table-styling">
                    <thead>
                        <tr class="table-primary">
                            <th>Tanggal</th>
                            <th>Shift</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($jadwalMendatang as $j)
                            <tr>
                                <td>{{ date('d-m-Y', strtotime($j->tanggal)) }}</td>
                                <td>{{ $j->shift ? $j->shift->nama_shift . ' (' . $j->shift->jam_masuk . ' - ' . $j->shift->jam_keluar . ')' : '-' }}</td>
                                <td>{{ $j->status }}</td>
                                <td>{{ $j->keterangan }}</td>
                            </tr>
                        @endforeach
                        @if ($jadwalMendatang->isEmpty())
                            <tr><td colspan="4" class="text-center">Belum ada jadwal mendatang.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>

            @include('components.button-list')
        </div>
    </div>

@endsection
