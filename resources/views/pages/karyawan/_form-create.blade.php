<form action="{{ route('karyawan.store') }}" method="POST">
    @csrf
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Nama *</label>
        <div class="col-sm-10">
            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                placeholder="Nama lengkap karyawan" value="{{ old('nama') }}">
            @error('nama')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Jabatan</label>
        <div class="col-sm-10">
            <input type="text" name="jabatan" class="form-control @error('jabatan') is-invalid @enderror"
                placeholder="Jabatan, mis. Staff Admin" value="{{ old('jabatan') }}">
            @error('jabatan')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Departemen</label>
        <div class="col-sm-10">
            <input type="text" name="departemen" class="form-control @error('departemen') is-invalid @enderror"
                placeholder="Departemen / divisi" value="{{ old('departemen') }}">
            @error('departemen')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Tempat Lahir</label>
        <div class="col-sm-10">
            <input type="text" name="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror"
                placeholder="Tempat lahir" value="{{ old('tempat_lahir') }}">
            @error('tempat_lahir')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Tanggal Lahir</label>
        <div class="col-sm-10">
            <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror"
                placeholder="" value="{{ old('tanggal_lahir') }}">
            @error('tanggal_lahir')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Jenis Kelamin</label>
        <div class="col-sm-10">
            <select name="jenis_kelamin" class="form-control">
                <option value="">--Pilih--</option>
                <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Alamat</label>
        <div class="col-sm-10">
            <textarea name="alamat" class="form-control" placeholder="Alamat karyawan">{{ old('alamat') }}</textarea>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">No HP</label>
        <div class="col-sm-10">
            <input type="text" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror"
                placeholder="Nomor handphone" value="{{ old('no_hp') }}">
            @error('no_hp')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Email</label>
        <div class="col-sm-10">
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                placeholder="Email" value="{{ old('email') }}">
            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Tanggal Masuk *</label>
        <div class="col-sm-10">
            <input type="date" name="tanggal_masuk" class="form-control @error('tanggal_masuk') is-invalid @enderror"
                placeholder="" value="{{ old('tanggal_masuk') }}">
            @error('tanggal_masuk')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Status Karyawan *</label>
        <div class="col-sm-10">
            <select name="status_karyawan" class="form-control">
                <option value="Tetap" {{ old('status_karyawan') == 'Tetap' ? 'selected' : '' }}>Tetap</option>
                <option value="Kontrak" {{ old('status_karyawan') == 'Kontrak' ? 'selected' : '' }}>Kontrak</option>
                <option value="Harian" {{ old('status_karyawan') == 'Harian' ? 'selected' : '' }}>Harian</option>
                <option value="Magang" {{ old('status_karyawan') == 'Magang' ? 'selected' : '' }}>Magang</option>
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Aktif</label>
        <div class="col-sm-10">
            <input type="checkbox" name="aktif" value="1" checked> Karyawan aktif (ikut penjadwalan)
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">NPWP</label>
        <div class="col-sm-10">
            <input type="text" name="npwp" class="form-control @error('npwp') is-invalid @enderror"
                placeholder="NPWP" value="{{ old('npwp') }}">
            @error('npwp')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Nama Bank</label>
        <div class="col-sm-10">
            <input type="text" name="nama_bank" class="form-control @error('nama_bank') is-invalid @enderror"
                placeholder="Bank untuk transfer gaji" value="{{ old('nama_bank') }}">
            @error('nama_bank')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">No Rekening</label>
        <div class="col-sm-10">
            <input type="text" name="no_rekening" class="form-control @error('no_rekening') is-invalid @enderror"
                placeholder="Nomor rekening gaji" value="{{ old('no_rekening') }}">
            @error('no_rekening')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Gaji Pokok</label>
        <div class="col-sm-10">
            <input type="number" name="gaji_pokok" class="form-control @error('gaji_pokok') is-invalid @enderror"
                placeholder="Gaji pokok per bulan" value="{{ old('gaji_pokok') }}">
            @error('gaji_pokok')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <button type="submit" class="btn btn-sm btn-primary"><i class="feather icon-save"></i>Simpan</button>
</form>
