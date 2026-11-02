<form action="{{ url('general-ledger/arus-kas') }}" method="POST">
    @csrf
    <div class="row">
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
