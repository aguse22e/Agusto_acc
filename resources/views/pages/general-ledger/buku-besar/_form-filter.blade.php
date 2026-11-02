<form action="{{ url('general-ledger/buku-besar') }}" method="POST">
    @csrf
    <div class="row">

        <div class="col-md-4 mb-4">
            <label for="">Kode Akun</label>
            <select name="kodeAkun[]" class="js-example-basic-multiple col-sm-12" style="width: 100%;" multiple required>
                <option value="">--Pilih Akun--</option>
                <option value="all" {{$isAll ? 'selected' : ''}}>Semua Akun</option>
                @foreach ($allAkun as $item)
                    <option value="{{$item->kode_akun}}" {{isset($kodeAkun) && !$isAll && in_array($item->kode_akun, $selectedAkun) ? 'selected' : ''}} >{{$item->kode_akun . ' ~ '.$item->nama}}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3 mb-4">
            <label for="">Dari</label>
            <input type="date" name="tanggalDari" autocomplete="off" class="form-control form-control-lg" value="{{isset($dari) ? $dari : date('Y-m-d') }}" required>
        </div>

        <div class="col-md-3 mb-4">
            <label for="">Sampai</label>
            <input type="date" name="tanggalSampai" autocomplete="off" class="form-control form-control-lg" value="{{isset($sampai) ? $sampai : date('Y-m-d') }}" required>
        </div>

        <div class="col-md-2 mt-4">
            <button type="submit" class="btn btn-primary"> <i class="fas fa-filter"></i> Filter</button>
        </div>

    </div>
</form>
