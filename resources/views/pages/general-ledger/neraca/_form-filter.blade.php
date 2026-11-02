<form action="{{ url('general-ledger/neraca') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-4 mb-4">
            <label for="">Per Tanggal</label>
            <input type="date" name="tanggalSampai" autocomplete="off" class="form-control form-control-lg" value="{{isset($sampai) ? $sampai : date('Y-m-d') }}" required>
        </div>

        <div class="col-md-2 mt-4">
            <button type="submit" class="btn btn-primary"> <i class="fas fa-filter"></i> Filter</button>
        </div>
    </div>
</form>
