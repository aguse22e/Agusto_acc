<div class="form-group row row-detail" data-no="{{ $no }}">
    <input type="hidden" name='id_detail[]' class='idDetail' value='{{$idDetail}}'>
    <div class="col-sm-4 mt-4">
        {{-- <div class="d-flex"> --}}
            <label class="col-form-label">Lawan</label>
        {{-- </div> --}}
        <select name="kode_lawan[]" class="form-control select2 js-example-basic-single {{ isset($n)&&$errors->has('kode_lawan.'.$n) ? ' is-invalid' : '' }}" style="width: 100%" disabled>
            <option value=""> -- Pilih --</option>
            @foreach ($kode_lawan as $item)
                <option value="{{ $item->kode_akun }}" {{ old($fields['kode_lawan'], isset($edit) ?  $edit['kode_lawan'] : '') == $item->kode_akun ? 'selected' : ''}}>{{ $item->kode_akun.'--'.$item->nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-sm-4 mt-4">
        <label class="col-form-label">Nominal</label>
        <input type="number" step=".01" name="subtotal[]" value="{{ old($fields['subtotal'], isset($edit) ? $edit['subtotal'] : '') }}" class="form-control form-control-lg getTotalKas {{ isset($n)&&$errors->has('subtotal.'.$n) ? ' is-invalid' : '' }}" readonly>
    </div>
    <div class="col-sm-3 mt-4">
        <label class="col-form-label">Keterangan</label>
        <input type="text" name="keterangan[]" value="{{ old($fields['keterangan'], isset($edit) ? str_replace('-', ' ', $edit['keterangan']) : '') }}" class="form-control form-control-lg {{ isset($n)&&$errors->has('keterangan.'.$n) ? ' is-invalid' : '' }}" readonly>
    </div>
</div>

