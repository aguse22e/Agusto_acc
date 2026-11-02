<div class="table-responsive">
    <table class="table table-styling table-de">
        <thead>
            <tr class="table-primary">
                <th class="text-center">#</th>
                <th>Jenis Transaksi</th>
                <th>Tanggal Kunci</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $page = Request::get('page');
                $no = !$page || $page == 1 ? 1 : ($page - 1) * 10 + 1;
            @endphp
            @foreach ($kunci_transaksi as $item)
                <tr class="border-bottom-primary">
                    <td class="text-center text-muted">{{ $no }}</td>
                    <td>{{ $item->jenis_transaksi }}</td>
                    <td>{{ date('d-m-Y', strtotime($item->tanggal_mulai_kunci)) }}</td>
                    <td>
                        <div class="form-inline">
                            <a href="{{ route('kunci-transaksi.edit', $item->id) }}" class="mr-2">
                                <button type="button" id="PopoverCustomT-1" class="btn btn-primary btn-sm"
                                    data-toggle="tooltip" title="Edit" data-placement="top"><span
                                        class="feather icon-edit"></span></button>
                            </a>
                        </div>
                    </td>
                </tr>
                @php
                    $no++;
                @endphp
            @endforeach
        </tbody>
    </table>
    <div class="pull-right">
        {{ $kunci_transaksi->appends(Request::all())->links('vendor.pagination.custom') }}
    </div>
</div>
