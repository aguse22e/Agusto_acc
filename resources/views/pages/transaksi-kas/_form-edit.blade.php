<form action="{{ route('kas-transaksi.update',$transaksi_kas->kode_transaksi_kas) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group row">
        <div class="col-sm-4">
            <label class="col-form-label">Kode Transaksi Kas</label>
            <input type="text" name="kode_transaksi_kas" class="form-control form-control-lg {{ $errors->has('kode_transaksi_kas') ? ' is-invalid' : '' }}"
            placeholder="Kode Transaksi Kas" value="{{ old('kode_transaksi_kas',$transaksi_kas->kode_transaksi_kas) }}" readonly>
            @if ($errors->has('kode_transaksi_kas'))
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @endif
        </div>
        <div class="col-sm-4">
            <label class="col-form-label">Tanggal</label>
            <input class="form-control form-control-lg {{ $errors->has('tanggal') ? ' is-invalid' : '' }}" type="date" name="tanggal" value="{{ old('tanggal',$transaksi_kas->tanggal) }}"/>
            @if ($errors->has('tanggal'))
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <div class="col-sm-4 ">
            <label class="col-form-label">Tipe</label>
            <select name="tipe" id="tipe" class="select2 form-control js-example-basic-single @error('tipe') is-invalid @enderror" style="width: 100%">
                <option value="0"> --Pilih--</option>
                <option value="Masuk" {{old('tipe',$transaksi_kas->tipe, $transaksi_kas->tipe) == 'Masuk' ? 'selected' : ''}}>Masuk</option>
                <option value="Keluar" {{old('tipe',$transaksi_kas->tipe, $transaksi_kas->tipe) == 'Keluar' ? 'selected' : ''}}>Keluar</option>
            </select>
            @if ($errors->has('tipe'))
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @endif
        </div>
        <div class="col-sm-4">
            <label class="col-form-label">Kode Akun Kas</label>
            <select name="kode_akun" id="kode_akun" class="select2 form-control js-example-basic-single @error('kode_akun') is-invalid @enderror" style="width: 100%">
                <option value="0"> --Pilih Kode Akun--</option>
                @foreach ($kodeAkun as $item)
                    <option value="{{ $item->kode_akun }}" {{old('kode_akun',$transaksi_kas->akun_kode) == $item->kode_akun ? 'selected' : ''}}>{{ $item->kode_akun.'--'.$item->nama }}</option>
                @endforeach
            </select>
            @if ($errors->has('kode_akun'))
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @endif
        </div>
    </div>
    <div class="card-header">
        <h5>Detail Transaksi Kas</h5>
    </div>
    <div class="detail-lawan">
        <div class="" id='urlAddDetail' data-url="{{ url('kas/kas-transaksi/addEditDetailKasTransaksi') }}">
            {{-- @if (!is_null(old('kode_lawan'))) --}}
            @if (!is_null(old('kode_lawan')))
                @php
                    $loop = array();
                    foreach(old('kode_lawan') as $i => $val){
                        $loop[] = array(
                        'kode_lawan' => old('kode_lawan.'.$i),
                        'subtotal' => (float)old('subtotal.'.$i),
                        'keterangan' => old('keterangan.'.$i),
                        );
                    }
                @endphp

            @else
                @php
                    $loop = $transaksi_kas_detail;
                @endphp
            @endif
            @php $no = 0; $total = 0; @endphp

            @foreach ($loop as $n => $edit)
                @php
                    $no++;
                    $linkHapus = $no==1 ? false : true;
                    $harga = 0;
                    $fields = array(
                        'kode_lawan' => 'kode_lawan.'.$n,
                        'subtotal' => 'subtotal.'.$n,
                        'keterangan' => 'keterangan.'.$n,
                    );

                    if(!is_null(old('kode_lawan'))){
                        $total = $total + $edit['subtotal'];
                        $idDetail = old('id_detail.'.$n);
                    }
                    else{
                        $total = $total + $edit['subtotal'];
                        $idDetail = $edit['id'];
                    }
                @endphp
                @include('pages.transaksi-kas.form-edit-detail-transaksi-kas',['hapus' => $linkHapus, 'no' => $no, 'kode_lawan' => $kode_lawan])
                        {{-- @include('pages.transaksi-kas.form-detail-transaksi-kas'); --}}
            @endforeach

        </div>
       <h5 class='text-right mt-1 pr-5' style="font-weight: bold">Total : <span id='total' class="text-info" style="font-weight: bold">{{number_format($total, 2, ',', '.')}}</span></h5>
    </div>
    <button type="submit" class="btn btn-sm btn-primary"><i class="feather icon-save"></i>Simpan</button>
    <button type="reset" class="btn btn-sm btn-default"> <span class="fa fa-times"></span> Cancel</button>
</form>

@push('custom-scripts')

@endpush
