<div class="form-group row row-detail" data-no="{{ $no }}">
    <div class="col-sm-4 mt-4">
        <div class="d-flex">
            <label class="col-form-label">Lawan</label>
        </div>
        <select name="kode_lawan[]" class="form-control select2 js-example-basic-single {{ isset($n)&&$errors->has('kode_lawan.'.$n) ? ' is-invalid' : '' }}" style="width: 100%">
            <option value=""> -- Pilih --</option>
            @foreach ($kode_lawan as $item)
                <option value="{{ $item->kode_akun }}" {{ isset($n)&&old('kode_lawan.'.$n) == $item->kode_akun ? 'selected' : ''}}>{{ $item->nama }}</option>
            @endforeach
        </select>
        @if(isset($n)&&$errors->has('kode_lawan.'.$n))
            <div class="invalid-feedback">
                {{ $errors->first('kode_lawan.'.$n) }}
            </div>
        @endif
    </div>
    <div class="col-sm-3 mt-4">
        <label class="col-form-label">Nominal</label>
        <input type="number" step=".01" name="subtotal[]" value="{{ isset($n) ? old('subtotal.'.$n) : ''}}" class="form-control form-control-lg getTotalKas {{ isset($n)&&$errors->has('subtotal.'.$n) ? ' is-invalid' : '' }}">
        @if(isset($n)&&$errors->has('subtotal.'.$n))
            <div class="invalid-feedback">
                {{ $errors->first('subtotal.'.$n) }}
            </div>
        @endif
    </div>
    <div class="col-sm-4 mt-4">
        <label class="col-form-label">Keterangan</label>
        <input type="text" name="keterangan[]" value="{{ isset($n) ? old('keterangan.'.$n) : ''}}" class="form-control form-control-lg {{ isset($n)&&$errors->has('keterangan.'.$n) ? ' is-invalid' : '' }}">
        @if(isset($n)&&$errors->has('keterangan.'.$n))
            <div class="invalid-feedback">
                {{ $errors->first('keterangan.'.$n) }}
            </div>
        @endif
    </div>
    <div class="col-sm-1 mt-4">
        <div class="col-md-1" style="margin-top: 35px">
            <div class="d-flex">
                <div class="p-2">
                    <a class="addDetail" data-no='{{ $no }}' href=""><i class="fa fa-plus-square text-primary"></i></a>
                </div>
                @if ($hapus)
                <div class="p-2">
                    <a class="deleteDetail" data-no='{{ $no }}' href=""><i class="fa fa-minus-square text-danger "></i></a>
                </div>
                @endif
                 {{-- @if($hapus) --}}
                {{-- @endif --}}
            </div>
        </div>
    </div>
</div>

