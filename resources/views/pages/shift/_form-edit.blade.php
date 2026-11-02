<form action="{{ route('shift.update', $shift->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Nama Shift *</label>
        <div class="col-sm-10">
            <input type="text" name="nama_shift" class="form-control @error('nama_shift') is-invalid @enderror"
                placeholder="Nama shift, mis. Pagi" value="{{ old('nama_shift', $shift->nama_shift) }}">
            @error('nama_shift')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Jam Masuk *</label>
        <div class="col-sm-10">
            <input type="time" name="jam_masuk" class="form-control @error('jam_masuk') is-invalid @enderror"
                value="{{ old('jam_masuk', $shift->jam_masuk) }}">
            @error('jam_masuk')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Jam Keluar *</label>
        <div class="col-sm-10">
            <input type="time" name="jam_keluar" class="form-control @error('jam_keluar') is-invalid @enderror"
                value="{{ old('jam_keluar', $shift->jam_keluar) }}">
            @error('jam_keluar')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Keterangan</label>
        <div class="col-sm-10">
            <input type="text" name="keterangan" class="form-control"
                placeholder="Keterangan shift" value="{{ old('keterangan', $shift->keterangan) }}">
        </div>
    </div>
    <button type="submit" class="btn btn-sm btn-primary"><i class="feather icon-save"></i>Simpan</button>
</form>
