<form action="{{ route('customer.update', $data->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Kode</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" value="{{ $data->kode_customer }}" readonly>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Nama *</label>
        <div class="col-sm-10">
            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                placeholder="Nama customer" value="{{ old('nama', $data->nama) }}">
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
                placeholder="Nama kontak person" value="{{ old('kontak_person', $data->kontak_person) }}">
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
                placeholder="Nomor handphone" value="{{ old('no_hp', $data->no_hp) }}">
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
                placeholder="Telepon kantor" value="{{ old('telepon', $data->telepon) }}">
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
                placeholder="Email" value="{{ old('email', $data->email) }}">
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
            <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" placeholder="Alamat customer">{{ old('alamat', $data->alamat) }}</textarea>
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
                placeholder="Kota" value="{{ old('kota', $data->kota) }}">
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
                placeholder="Provinsi" value="{{ old('provinsi', $data->provinsi) }}">
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
                placeholder="Kode pos" value="{{ old('kode_pos', $data->kode_pos) }}">
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
                placeholder="NPWP" value="{{ old('npwp', $data->npwp) }}">
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
                placeholder="Termin pembayaran, mis. 30" value="{{ old('termin_hari', $data->termin_hari) }}">
            @error('termin_hari')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Saldo Awal Piutang</label>
        <div class="col-sm-10">
            <input type="number" name="piutang" class="form-control @error('piutang') is-invalid @enderror"
                placeholder="Saldo awal" value="{{ old('piutang', $data->piutang) }}">
            @error('piutang')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Kategori</label>
        <div class="col-sm-10">
            <input type="text" name="kategori" class="form-control" placeholder="Kategori, mis. Umum / Reseller / Distributor" value="{{ old('kategori', $data->kategori) }}">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Limit Kredit</label>
        <div class="col-sm-10">
            <input type="number" name="limit_kredit" class="form-control" placeholder="Limit kredit (0 = tanpa batas khusus)" value="{{ old('limit_kredit', $data->limit_kredit) }}">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Akun Piutang (default)</label>
        <div class="col-sm-10">
            <select name="akun_piutang" class="form-control">
                <option value="">--Pilih Akun--</option>
                @foreach ($semuaAkun as $a)
                    <option value="{{ $a->kode_akun }}" {{ old('akun_piutang', $data->akun_piutang) == $a->kode_akun ? 'selected' : '' }}>{{ $a->kode_akun . ' ~ ' . $a->nama }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Catatan</label>
        <div class="col-sm-10">
            <textarea name="catatan" class="form-control" placeholder="Catatan">{{ old('catatan', $data->catatan) }}</textarea>
        </div>
    </div>
    <button type="submit" class="btn btn-sm btn-primary"><i class="feather icon-save"></i>Simpan</button>
</form>
