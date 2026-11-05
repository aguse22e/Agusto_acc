<form action="{{ route('kode-akun.store') }}" method="POST">
    @csrf
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Kode Induk</label>
        <div class="col-sm-10">
            <select name="induk_kode" id="induk_kode" class="form-control @error('induk_kode') is-invalid @enderror">
                <option value="0">Pilih Kode Induk</option>
                @foreach ($data as $item)
                    <option value="{{ $item->kode_induk }}">{{ $item->kode_induk.' -- '.$item->nama }}</option>
                @endforeach
            </select>
            @error('induk_kode')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Kode Akun</label>
        <div class="col-sm-10">
            <input type="text" name="kode_akun" class="form-control @error('kode_akun') is-invalid @enderror"
                placeholder="Kode Akun" value="{{ old('kode_akun') }}">
            @error('kode_akun')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Nama Kode</label>
        <div class="col-sm-10">
            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                placeholder="Nama kode induk" value="{{ old('nama') }}">
            @error('nama')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Tipe</label>
        <div class="col-sm-10">
            <select name="tipe" id="tipe" class="form-control @error('tipe') is-invalid @enderror">
                <option value="0">Pilih tipe</option>
                <option value="Debit" {{ old('tipe') == 'Administrator' ? ' selected' : '' }}>Debit</option>
                <option value="Kredit" {{ old('tipe') == 'Accounting' ? ' selected' : '' }}>Kredit</option>
            </select>
            @error('tipe')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Jenis Akun</label>
        <div class="col-sm-10">
            <select name="jenis_akun" id="jenis_akun" class="form-control @error('jenis_akun') is-invalid @enderror">
                <option value="Lainnya" {{ old('jenis_akun', 'Lainnya') == 'Lainnya' ? ' selected' : '' }}>Lainnya</option>
                <option value="Kas" {{ old('jenis_akun') == 'Kas' ? ' selected' : '' }}>Kas</option>
                <option value="Bank" {{ old('jenis_akun') == 'Bank' ? ' selected' : '' }}>Bank</option>
            </select>
            <small class="form-text text-muted">Penanda sifat akun. Akun utama Transaksi Kas harus berjenis Kas dan akun utama Transaksi Bank harus berjenis Bank.</small>
            @error('jenis_akun')
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
