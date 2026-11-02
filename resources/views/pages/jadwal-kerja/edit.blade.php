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
                    <h5>Edit Jadwal — {{ $jadwal->karyawan->nama }} ({{ date('d-m-Y', strtotime($jadwal->tanggal)) }})</h5>
                </div>
                <div class="card-block">
                    <form action="{{ route('jadwal-kerja.update', $jadwal->id) }}" method="POST">
                        @csrf
                        @method('PUT')
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Karyawan *</label>
        <div class="col-sm-10">
            <select name="karyawan_id" class="form-control">
                @foreach ($semuaKaryawan as $k)
                    <option value="{{ $k->id }}" {{ old('karyawan_id', $jadwal->karyawan_id) == $k->id ? 'selected' : '' }}>{{ $k->kode_karyawan . ' ~ ' . $k->nama }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Tanggal *</label>
        <div class="col-sm-10">
            <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', $jadwal->tanggal->format('Y-m-d')) }}">
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
                    <option value="{{ $s->id }}" {{ old('shift_id', $jadwal->shift_id) == $s->id ? 'selected' : '' }}>{{ $s->nama_shift . ' (' . $s->jam_masuk . ' - ' . $s->jam_keluar . ')' }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Status *</label>
        <div class="col-sm-10">
            <select name="status" class="form-control">
                @foreach ($daftarStatus as $st)
                    <option value="{{ $st }}" {{ old('status', $jadwal->status) == $st ? 'selected' : '' }}>{{ $st }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Keterangan</label>
        <div class="col-sm-10">
            <input type="text" name="keterangan" class="form-control" placeholder="Keterangan (opsional)" value="{{ old('keterangan', $jadwal->keterangan) }}">
        </div>
    </div>
                        <button type="submit" class="btn btn-sm btn-primary"><i class="feather icon-save"></i>Simpan</button>
                    </form>
                    <form action="{{ route('jadwal-kerja.destroy', $jadwal->id) }}" method="post" class="mt-3">
                        @csrf
                        @method('delete')
                        <button type="button" class="btn btn-danger btn-sm"
                            onclick="confirm('{{ __('Move data to trash ?') }}') ? this.parentElement.submit() : ''">
                            <span class="feather icon-trash"></span> Pindahkan ke Sampah
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
