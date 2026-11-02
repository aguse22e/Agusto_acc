<form>
    <div class="form-group row">
        <div class="col-sm-4">
            <label class="col-form-label">Kode Memorial</label>
            <input type="text" name="kode_memorial" class="form-control form-control-lg {{ $errors->has('kode_memorial') ? ' is-invalid' : '' }}"
                value="{{ old('kode_memorial',$memorial->kode_memorial) }}" readonly>
        </div>
        <div class="col-sm-4">
            <label class="col-form-label">Tanggal</label>
            <input class="form-control form-control-lg {{ $errors->has('tanggal') ? ' is-invalid' : '' }}" type="date" name="tanggal" value="{{ old('tanggal', $memorial->tanggal) }}" readonly/>
        </div>
        <div class="col-sm-4 ">
            <label class="col-form-label">Tipe</label>
            <select name="tipe" id="tipe" class="select2 form-control js-example-basic-single {{ $errors->has('tipe') ? ' is-invalid' : '' }}" style="width: 100%" disabled>
                <option value="0"> --Pilih--</option>
                <option value="Masuk" {{old('tipe', $memorial->tipe) == 'Masuk' ? 'selected' : ''}} >Masuk</option>
                <option value="Keluar" {{old('tipe', $memorial->tipe) == 'Keluar' ? 'selected' : ''}}>Keluar</option>
            </select>
        </div>

    </div>
    <div class="card-header">
        <h5>Detail Transaksi Memorial</h5>
    </div>
    <div class="detail-lawan">
        <div class="" id='urlAddDetail' data-url="{{ url('memorial/memorial/addEditDetailMemorial') }}">
            {{-- @if (!is_null(old('kode_lawan'))) --}}
            @if(!is_null(old('lawan')))
                @php
                    $loop = array();
                    foreach(old('lawan') as $i => $val){
                        $loop[] = array(
                        'kode' => old('kode.'.$i),
                        'lawan' => old('lawan.'.$i),
                        'subtotal' => (float)old('subtotal.'.$i),
                        'keterangan' => old('keterangan.'.$i),
                        );
                    }
                @endphp
            @else
                @php
                    $loop = $detailMemorial;
                @endphp
            @endif

            @php $no = 0; $total = 0; @endphp
            @foreach($loop as $n => $edit)
                @php
                $no++;
                $linkHapus = $no==1 ? false : true;
                $harga = 0;
                $fields = array(
                    'kode' => 'kode.'.$n,
                    'lawan' => 'lawan.'.$n,
                    'subtotal' => 'subtotal.'.$n,
                    'keterangan' => 'keterangan.'.$n,
                );

                if(!is_null(old('lawan'))){
                    $total = $total + $edit['subtotal'];
                    $idDetail = old('id_detail.'.$n);
                }
                else{
                    $total = $total + $edit['subtotal'];
                    $idDetail = $edit['id'];
                }
                @endphp
                @include('pages.memorial.form-show-detail-memorial',['hapus' => $linkHapus, 'no' => $no, 'kodeAkun' => $kodeAkun])
            @endforeach
            @php
                // $total = $total;
            @endphp
        </div>
       <h5 class='text-right mt-1 pr-5' style="font-weight: bold">Total : <span id='total' class="text-info" style="font-weight: bold">{{number_format($total,0,',','.')}}</span></h5>
    </div>
</form>

@push('custom-scripts')

@endpush
