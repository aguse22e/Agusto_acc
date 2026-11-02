<form action="{{ route('update_password', $user->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Password Lama</label>
        <div class="col-sm-10">
            <input type="password" name="old_pass" class="form-control @error('old_pass') is-invalid @enderror"
                placeholder="Password lama" value="{{ old('old_pass') }}">
            @error('old_pass')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Password Baru</label>
        <div class="col-sm-10">
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                placeholder="Password baru" value="{{ old('password') }}">
            @error('password')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Konfirmasi Password Baru</label>
        <div class="col-sm-10">
            <input type="password" name="confirmation" class="form-control @error('confirmation') is-invalid @enderror"
                placeholder="Konfirmasi password baru" value="{{ old('confirmation') }}">
            @error('confirmation')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <button type="submit" class="btn btn-sm btn-primary"><i class="feather icon-save"></i>Simpan</button>
</form>
