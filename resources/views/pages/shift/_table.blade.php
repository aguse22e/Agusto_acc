<div class="table-responsive">
    <table class="table table-styling table-de">
        <thead>
            <tr class="table-primary">
                <th class="text-center">#</th>
                <th>Nama Shift</th>
                <th>Jam Masuk</th>
                <th>Jam Keluar</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $page = Request::get('page');
                $no = !$page || $page == 1 ? 1 : ($page - 1) * 10 + 1;
            @endphp
            @foreach ($shift as $item)
                <tr class="border-bottom-primary">
                    <td class="text-center text-muted">{{ $no }}</td>
                    <td>{{ $item->nama_shift }}</td>
                    <td>{{ $item->jam_masuk }}</td>
                    <td>{{ $item->jam_keluar }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td>
                        <div class="form-inline">
                            <a href="{{ route('shift.show', $item->id) }}" class="mr-2">
                                <button type="button" class="btn btn-info btn-sm"
                                    data-toggle="tooltip" title="Lihat" data-placement="top"><span
                                        class="feather icon-eye"></span></button>
                            </a>
                            <a href="{{ route('shift.edit', $item->id) }}" class="mr-2">
                                <button type="button" id="PopoverCustomT-1" class="btn btn-primary btn-sm"
                                    data-toggle="tooltip" title="Edit" data-placement="top"><span
                                        class="feather icon-edit"></span></button>
                            </a>
                            <form action="{{ route('shift.destroy', $item->id) }}" method="post">
                                @csrf
                                @method('delete')
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Hapus"
                                    data-placement="top"
                                    onclick="confirm('{{ __('Move data to trash ?') }}') ? this.parentElement.submit() : ''">
                                    <span class="feather icon-trash"></span>
                                </button>
                            </form>
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
        {{ $shift->appends(Request::all())->links('vendor.pagination.custom') }}
    </div>
</div>
