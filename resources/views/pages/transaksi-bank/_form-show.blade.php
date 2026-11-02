<form>
    <div class="form-group row">
        <div class="col-sm-4">
            <label class="col-form-label">Kode Transaksi Bank</label>
            <input type="text" name="kode_transaksi_bank" class="form-control form-control-lg {{ $errors->has('kode_transaksi_bank') ? ' is-invalid' : '' }}"
            placeholder="Kode Transaksi Bank" value="{{ old('kode_transaksi_bank',$transaksi_bank->kode_transaksi_bank) }}" readonly>
        </div>
        <div class="col-sm-4">
            <label class="col-form-label">Tanggal</label>
            <input class="form-control form-control-lg {{ $errors->has('tanggal') ? ' is-invalid' : '' }}" type="date" name="tanggal" value="{{ old('tanggal',$transaksi_bank->tanggal) }}" readonly />
        </div>
        <div class="col-sm-4 ">
            <label class="col-form-label">Tipe</label>
            <select name="tipe" id="tipe" class="select2 form-control js-example-basic-single @error('tipe') is-invalid @enderror" style="width: 100%" disabled>
                <option value="0"> --Pilih--</option>
                <option value="Masuk" {{old('tipe',$transaksi_bank->tipe, $transaksi_bank->tipe) == 'Masuk' ? 'selected' : ''}}>Masuk</option>
                <option value="Keluar" {{old('tipe',$transaksi_bank->tipe, $transaksi_bank->tipe) == 'Keluar' ? 'selected' : ''}}>Keluar</option>
            </select>
        </div>
        <div class="col-sm-4">
            <label class="col-form-label">Kode Akun Bank</label>
            <select name="kode_akun" id="kode_akun" class="select2 form-control js-example-basic-single @error('kode_akun') is-invalid @enderror" style="width: 100%" disabled>
                <option value="0"> --Pilih Kode Akun--</option>
                @foreach ($kodeAkun as $item)
                    <option value="{{ $item->kode_akun }}" {{old('kode_akun',$transaksi_bank->akun_kode) == $item->kode_akun ? 'selected' : ''}}>{{ $item->kode_akun.'--'.$item->nama }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="card-header">
        <h5>Detail Transaksi Bank</h5>
    </div>
    <div class="detail-lawan">
        <div class="" id='urlAddDetail' data-url="{{ url('bank/bank-transaksi/addEditDetailBankTransaksi') }}">
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
                    $loop = $transaksi_bank_detail;
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
                @include('pages.transaksi-bank.form-show-detail-transaksi-bank',['hapus' => $linkHapus, 'no' => $no, 'kode_lawan' => $kode_lawan])
                        {{-- @include('pages.transaksi-bank.form-detail-transaksi-bank'); --}}
            @endforeach

        </div>
       <h5 class='text-right mt-1 pr-5' style="font-weight: bold">Total : <span id='total' class="text-info" style="font-weight: bold">{{number_format($total, 2, ',', '.')}}</span></h5>
    </div>
</form>

@push('custom-scripts')

@endpush
