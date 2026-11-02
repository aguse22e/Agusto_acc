<form action="{{ route('supplier.store') }}" method="POST">
    @csrf
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Nama *</label>
        <div class="col-sm-10">
            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                placeholder="Nama supplier" value="{{ old('nama') }}">
            @error('nama')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Kontak Person</label>
        <div class="col-sm-10">
            <input type="text" name="kontak_person" class="form-control @error('kontak_person') is-invalid @enderror"
                placeholder="Nama kontak person" value="{{ old('kontak_person') }}">
            @error('kontak_person')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">No HP *</label>
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
        <label class="col-sm-2 col-form-label">Telepon</label>
        <div class="col-sm-10">
            <input type="text" name="telepon" class="form-control @error('telepon') is-invalid @enderror"
                placeholder="Telepon kantor" value="{{ old('telepon') }}">
            @error('telepon')
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
        <label class="col-sm-2 col-form-label">Alamat *</label>
        <div class="col-sm-10">
            <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" placeholder="Alamat supplier">{{ old('alamat') }}</textarea>
            @error('alamat')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Kota</label>
        <div class="col-sm-10">
            <input type="text" name="kota" class="form-control @error('kota') is-invalid @enderror"
                placeholder="Kota" value="{{ old('kota') }}">
            @error('kota')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Provinsi</label>
        <div class="col-sm-10">
            <input type="text" name="provinsi" class="form-control @error('provinsi') is-invalid @enderror"
                placeholder="Provinsi" value="{{ old('provinsi') }}">
            @error('provinsi')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Kode Pos</label>
        <div class="col-sm-10">
            <input type="text" name="kode_pos" class="form-control @error('kode_pos') is-invalid @enderror"
                placeholder="Kode pos" value="{{ old('kode_pos') }}">
            @error('kode_pos')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
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
        <label class="col-sm-2 col-form-label">Termin (hari)</label>
        <div class="col-sm-10">
            <input type="number" name="termin_hari" class="form-control @error('termin_hari') is-invalid @enderror"
                placeholder="Termin pembayaran, mis. 30" value="{{ old('termin_hari') }}">
            @error('termin_hari')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Saldo Awal Hutang</label>
        <div class="col-sm-10">
            <input type="number" name="hutang" class="form-control @error('hutang') is-invalid @enderror"
                placeholder="Saldo awal" value="{{ old('hutang') }}">
            @error('hutang')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Nama Bank</label>
        <div class="col-sm-10">
            <input type="text" name="nama_bank" class="form-control" placeholder="Bank tujuan pembayaran" value="{{ old('nama_bank') }}">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">No Rekening</label>
        <div class="col-sm-10">
            <input type="text" name="no_rekening" class="form-control" placeholder="Nomor rekening pembayaran" value="{{ old('no_rekening') }}">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Akun Hutang (default)</label>
        <div class="col-sm-10">
            <select name="akun_hutang" class="form-control">
                <option value="">--Pilih Akun--</option>
                @foreach ($semuaAkun as $a)
                    <option value="{{ $a->kode_akun }}" {{ old('akun_hutang') == $a->kode_akun ? 'selected' : '' }}>{{ $a->kode_akun . ' ~ ' . $a->nama }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Catatan</label>
        <div class="col-sm-10">
            <textarea name="catatan" class="form-control" placeholder="Catatan">{{ old('catatan') }}</textarea>
        </div>
    </div>
    <button type="submit" class="btn btn-sm btn-primary"><i class="feather icon-save"></i>Simpan</button>
</form>
