<form action="{{ url('general-ledger/laba-rugi') }}" method="POST">
    @csrf
    <div class="row">

        <div class="col-md-4 mb-4">
            <label for="">Bulan</label>
            <select name="month" class="js-example-basic-single col-sm-12" style="width: 100%;" required>
                <option value="">--Pilih Bulan--</option>
                @foreach ($allBulan as $item)
                    <option value="{{ $item['bulan'] }}"
                        {{ isset($month) && $item['bulan'] == $month ? 'selected' : '' }}>{{ $item['nama'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4 mb-4">
            <label for="">Tahun</label>
            <select name="year" class="js-example-basic-single col-sm-12" style="width: 100%;" required>
                <option value="">--Pilih Tahun--</option>
                @for ($y = 2022; $y <= date('Y'); $y++)
                    <option value="{{ $y }}" {{ isset($year) && $year == $y ? 'selected' : '' }}>
                        {{ $y }}</option>
                @endfor
            </select>
        </div>

        <div class="col-md-2 mt-4">
            <button type="submit" class="btn btn-primary"> <i class="fas fa-filter"></i> Filter</button>
        </div>

    </div>
</form>
