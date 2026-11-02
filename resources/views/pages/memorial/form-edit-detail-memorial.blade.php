<div class="form-group row row-detail" data-no="{{ $no }}">
    <input type="hidden" name='id_detail[]' class='idDetail' value='{{$idDetail}}'>
    <div class="col-sm-3 mt-4">
        {{-- <div class="d-flex"> --}}
            <label class="col-form-label">Kode Akun</label>
        {{-- </div> --}}
        <select name="kode[]" class="form-control select2 js-example-basic-single {{ isset($n)&&$errors->has('kode.'.$n) ? ' is-invalid' : '' }}" style="width: 100%">
            <option value=""> -- Pilih --</option>
            @foreach ($kodeAkun as $item)
              <option value="{{$item->kode_akun}}" {{ old($fields['kode'], isset($edit) ?  $edit['kode'] : '') == $item->kode_akun ? 'selected' : ''}}>{{$item->kode_akun.' -- '.$item->nama}}</option>
              {{-- <option value="{{ $item->kode_akun }}" {{ old($fields['kode'], isset($edit) ?  $edit['kode'] : '') == $item->kode_akun ? 'selected' : ''}}>{{ $item->kode_akun.'--'.$item->nama }}</option> --}}
            @endforeach
        </select>
        @if(isset($n)&&$errors->has('kode.'.$n))
            <div class="invalid-feedback">
                {{ $errors->first('kode.'.$n) }}
            </div>
        @endif
    </div>
    <div class="col-sm-3 mt-4">
        {{-- <div class="d-flex"> --}}
            <label class="col-form-label">Lawan</label>
        {{-- </div> --}}

        <select name="lawan[]" class="form-control select2 js-example-basic-single {{ isset($n)&&$errors->has('lawan.'.$n) ? ' is-invalid' : '' }}" style="width: 100%">
            <option value=""> -- Pilih --</option>
            @foreach ($kodeAkun as $item)
                <option value="{{$item->kode_akun}}" {{ old($fields['lawan'], isset($edit) ?  $edit['lawan'] : '') == $item->kode_akun ? 'selected' : ''}}>{{$item->kode_akun.' -- '.$item->nama}}</option>
            @endforeach
        </select>
        @if(isset($n)&&$errors->has('lawan.'.$n))
            <div class="invalid-feedback">
                {{ $errors->first('lawan.'.$n) }}
            </div>
        @endif
    </div>
    <div class="col-sm-3 mt-4">
        <label class="col-form-label">Nominal</label>
        <input type="number" step=".01" name="subtotal[]" value="{{ old($fields['subtotal'], isset($edit) ? $edit['subtotal'] : '') }}" class="form-control form-control-lg getTotalKas {{ isset($n)&&$errors->has('subtotal.'.$n) ? ' is-invalid' : '' }}">
        @if(isset($n)&&$errors->has('subtotal.'.$n))
            <div class="invalid-feedback">
                {{ $errors->first('subtotal.'.$n) }}
            </div>
        @endif
    </div>
    <div class="col-sm-2 mt-4">
        <label class="col-form-label">Keterangan</label>
        <input type="text" name="keterangan[]" value="{{ old($fields['keterangan'], isset($edit) ? str_replace('-', ' ', $edit['keterangan']) : '') }}" class="form-control form-control-lg {{ isset($n)&&$errors->has('keterangan.'.$n) ? ' is-invalid' : '' }}">
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
                    <a class="deleteDetail addDeleteId" data-no='{{ $no }}' href=""><i class="fa fa-minus-square text-danger "></i></a>
                </div>
                @endif
                 {{-- @if($hapus) --}}
                {{-- @endif --}}
            </div>
        </div>
    </div>
</div>

