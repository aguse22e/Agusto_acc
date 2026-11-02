<form action="{{ route('kunci-transaksi.update',$data->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Jenis Transaksi</label>
        <div class="col-sm-4">
            <input type="text" name="jenis_transaksi" class="form-control @error('jenis_transaksi') is-invalid @enderror"
                placeholder="Kode Akun" value="{{ old('jenis_transaksi',$data->jenis_transaksi) }}" readonly>
            @error('jenis_transaksi')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Tanggal Kunci</label>
        <div class="col-sm-4">
            {{-- <input type="text" name="jenis_transaksi" class="form-control @error('jenis_transaksi') is-invalid @enderror"
                placeholder="Kode Akun" value="{{ old('jenis_transaksi',$data->jenis_transaksi) }}" readonly> --}}
            <input class="form-control @error('tanggal_kunci') is-invalid @enderror" type="date" name="tanggal_kunci" value="{{ $data->tanggal_mulai_kunci }}"/>
            @error('tanggal_kunci')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <button type="submit" class="btn btn-sm btn-primary"><i class="feather icon-save"></i>Simpan</button>
</form>

@push('custom-scripts')

@endpush
