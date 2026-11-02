<form action="{{ route('memorial.store') }}" method="POST">
    @csrf
    <div class="form-group row">
        {{-- <div class="col-sm-4">
            <label class="col-form-label">Kode Transaksi Kas</label>
            <input type="text" name="kode_transaksi_kas" class="form-control form-control-lg @error('kode_transaksi_kas') is-invalid @enderror"
                value="{{ old('kode_transaksi_kas') }}">
            @error('kode_transaksi_kas')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div> --}}
        <div class="col-sm-4">
            <label class="col-form-label">Tanggal</label>
            <input class="form-control form-control-lg @error('tanggal') is-invalid @enderror" type="date" name="tanggal"/>
            @error('tanggal')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <div class="col-sm-4 ">
            <label class="col-form-label">Tipe</label>
            <select name="tipe" id="tipe" class="select2 form-control js-example-basic-single @error('tipe') is-invalid @enderror" style="width: 100%">
                <option value="0"> --Pilih--</option>
                <option value="Masuk" {{old('tipe') == 'Masuk' ? 'selected' : ''}}>Masuk</option>
                <option value="Keluar" {{old('tipe') == 'Keluar' ? 'selected' : ''}}>Keluar</option>
            </select>
            @error('tipe')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

    </div>
    <div class="card-header">
        <h5>Detail Transaksi Memorial</h5>
    </div>
    <div class="detail-lawan">
        <div class="" id='urlAddDetail' data-url="{{ url('memorial/memorial/addDetailMemorial') }}">
            {{-- @if (!is_null(old('kode_lawan'))) --}}
            @if (!is_null(old('kode_lawan')))
                @php
                    $no = 0;
                @endphp
                @foreach (old('kode_lawan') as $n => $value)
                    @php
                        $no++
                    @endphp
                        @include('pages.memorial.form-detail-memorial-kas',['hapus' => false, 'no' => $no, 'kode_lawan' => $kode_lawan])
                        {{-- @include('pages.memorial.form-detail-memorial-kas'); --}}
                @endforeach
            @else
                 {{-- {{ $no }} --}}
                    @include('pages.memorial.form-detail-memorial-kas',['hapus' => false, 'no' => 1, 'kode_lawan' => $kode_lawan])
                    {{-- @include('pages.transaksi-kas.form-detail-transaksi-kas'); --}}

            @endif
        </div>
       <h5 class='text-right mt-1 pr-5' style="font-weight: bold">Total : <span id='total' class="text-info" style="font-weight: bold">0</span></h5>
    </div>
    <button type="submit" class="btn btn-sm btn-primary"><i class="feather icon-save"></i>Simpan</button>
</form>

@push('custom-scripts')

@endpush
