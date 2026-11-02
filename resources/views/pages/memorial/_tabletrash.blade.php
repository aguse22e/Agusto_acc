<div class="table-responsive">
    <table class="table table-styling table-de">
        <thead>
            <tr class="table-primary">
                <th class="text-center">#</th>
                <th>Kode Memorial</th>
                <th>Tanggal</th>
                <th>Tipe</th>
                <th>Total</th>
                {!! Auth::user()->level != 'Viewer' ? "<th>Aksi</th>" : '' !!}
            </tr>
        </thead>
        <tbody>
            @php
                $page = Request::get('page');
                $no = !$page || $page == 1 ? 1 : ($page - 1) * 10 + 1;
            @endphp
            @foreach ($memorial as $item)
                <tr class="border-bottom-primary">
                    <td class="text-center text-muted">{{ $no }}</td>
                    <td>{{ $item->kode_memorial }}</td>
                    <td>{{ date('d-m-Y', strtotime($item->tanggal)) }}</td>
                    <td>{{ $item->tipe }}</td>
                    <td>Rp. {{number_format($item->total, 2, ',', '.') }}</td>
                    @if (Auth::user()->level != 'Viewer')
                        <td>
                            <div class="form-inline">
                                <a href="{{ route('transaksiMemorial.restore', $item->kode_memorial) }}" class="mr-2">
                                    <button type="button" id="PopoverCustomT-1" class="btn btn-primary btn-sm"
                                        data-toggle="tooltip" title="Restore" data-placement="top"><i class="ti-reload"></i></button>
                                </a>
                                <form action="{{ route('transaksiMemorial.hapusPermanen', [$item->kode_memorial]) }}" method="post" onsubmit="return confirm('Delete this data permanently ?')">
                                    @csrf
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" value="Delete" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Hapus Permanen"
                                        data-placement="top">
                                        <span class="feather icon-trash"></span>
                                    </button>
                                    {{-- </a> --}}
                                </form>
                            </div>
                        </td>

                    @endif
                </tr>
                @php
                    $no++;
                @endphp
            @endforeach
        </tbody>
    </table>
    <div class="pull-right">
        {{ $memorial->appends(Request::all())->links('vendor.pagination.custom') }}
    </div>
</div>
