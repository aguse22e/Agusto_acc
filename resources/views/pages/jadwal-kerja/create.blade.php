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
                    <h5>Tambah Jadwal</h5>
                </div>
                <div class="card-block">
                    <form action="{{ route('jadwal-kerja.store') }}" method="POST">
                        @csrf
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Karyawan *</label>
        <div class="col-sm-10">
            <select name="karyawan_id" class="form-control">
                @foreach ($semuaKaryawan as $k)
                    <option value="{{ $k->id }}" {{ old('karyawan_id') == $k->id ? 'selected' : '' }}>{{ $k->kode_karyawan . ' ~ ' . $k->nama }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Tanggal *</label>
        <div class="col-sm-10">
            <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', date('Y-m-d')) }}">
            @error('tanggal')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Shift</label>
        <div class="col-sm-10">
            <select name="shift_id" class="form-control">
                <option value="">— Tanpa shift / Libur —</option>
                @foreach ($semuaShift as $s)
                    <option value="{{ $s->id }}" {{ old('shift_id') == $s->id ? 'selected' : '' }}>{{ $s->nama_shift . ' (' . $s->jam_masuk . ' - ' . $s->jam_keluar . ')' }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Status *</label>
        <div class="col-sm-10">
            <select name="status" class="form-control">
                @foreach ($daftarStatus as $st)
                    <option value="{{ $st }}" {{ old('status', 'Terjadwal') == $st ? 'selected' : '' }}>{{ $st }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Keterangan</label>
        <div class="col-sm-10">
            <input type="text" name="keterangan" class="form-control" placeholder="Keterangan (opsional)" value="{{ old('keterangan') }}">
        </div>
    </div>
                        <button type="submit" class="btn btn-sm btn-primary"><i class="feather icon-save"></i>Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
