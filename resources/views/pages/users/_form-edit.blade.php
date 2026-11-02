<form action="{{ route('user.update',$data->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Nama</label>
        <div class="col-sm-10">
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                placeholder="Nama User" value="{{ old('name',$data->name) }}">
            @error('name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Username</label>
        <div class="col-sm-10">
            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                placeholder="Masukkan Username" value="{{ old('username',$data->username) }}">
            @error('username')
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
                placeholder="Email User" value="{{ old('email',$data->email) }}">
            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Level</label>
        <div class="col-sm-10">
            <select name="level" id="level" class="form-control @error('email') is-invalid @enderror">
                <option value="">Pilih Level</option>
                <option value="Administrator" {{ old('level',$data->level) == 'Administrator' ? ' selected' : '' }}>Administrator</option>
                <option value="Accounting" {{ old('level',$data->level) == 'Accounting' ? ' selected' : '' }}>Accounting</option>
            </select>
            @error('level')
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
