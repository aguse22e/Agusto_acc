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

    <div class="row">
        <div class="col-sm-12">
            @include('components.button-list', ['btnText' => $btnText, 'btnLink' => $btnLink])
            <div class="card">
                <div class="card-header">
                    <h5>Generate Jadwal Massal</h5>
                </div>
                <div class="card-block">
                    <p>Membuat entri jadwal berstatus <b>Terjadwal</b> untuk rentang tanggal sesuai pola hari. Tanggal yang sudah memiliki jadwal <b>tidak ditimpa</b> — dilewati dan dilaporkan jumlahnya.</p>
                    <form action="{{ route('jadwal-kerja.proses-generate') }}" method="POST">
                        @csrf
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Karyawan *</label>
                            <div class="col-sm-10">
                                <select name="karyawan_pilihan" class="form-control">
                                    <option value="semua">Semua Karyawan Aktif</option>
                                    @foreach ($semuaKaryawan as $k)
                                        <option value="{{ $k->id }}">{{ $k->kode_karyawan . ' ~ ' . $k->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Shift *</label>
                            <div class="col-sm-10">
                                <select name="shift_id" class="form-control">
                                    @foreach ($semuaShift as $s)
                                        <option value="{{ $s->id }}">{{ $s->nama_shift . ' (' . $s->jam_masuk . ' - ' . $s->jam_keluar . ')' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Dari Tanggal *</label>
                            <div class="col-sm-10">
                                <input type="date" name="tanggal_dari" class="form-control" value="{{ old('tanggal_dari', date('Y-m-01')) }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Sampai Tanggal *</label>
                            <div class="col-sm-10">
                                <input type="date" name="tanggal_sampai" class="form-control" value="{{ old('tanggal_sampai', date('Y-m-t')) }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Pola Hari *</label>
                            <div class="col-sm-10">
                                <select name="pola" class="form-control">
                                    <option value="senin-jumat">Senin – Jumat</option>
                                    <option value="senin-sabtu">Senin – Sabtu</option>
                                    <option value="semua">Semua Hari</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-success"><i class="feather icon-calendar"></i> Generate</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
