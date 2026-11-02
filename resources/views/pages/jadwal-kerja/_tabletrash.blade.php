<div class="table-responsive">
    <table class="table table-styling table-de">
        <thead>
            <tr class="table-primary">
                <th>Karyawan</th>
                <th>Tanggal</th>
                <th>Shift</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($jadwal as $item)
                <tr class="border-bottom-primary">
                    <td>{{ $item->karyawan ? $item->karyawan->nama : '-' }}</td>
                    <td>{{ date('d-m-Y', strtotime($item->tanggal)) }}</td>
                    <td>{{ $item->shift ? $item->shift->nama_shift : '-' }}</td>
                    <td>{{ $item->status }}</td>
                    <td>
                        <div class="form-inline">
                            <a href="{{ route('jadwal-kerja.restore', $item->id) }}" class="mr-2">
                                <button type="button" class="btn btn-success btn-sm"
                                    data-toggle="tooltip" title="Restore" data-placement="top"><span
                                        class="feather icon-rotate-ccw"></span></button>
                            </a>
                            <form action="{{ route('jadwal-kerja.hapusPermanen', $item->id) }}" method="post">
                                @csrf
                                @method('delete')
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Hapus Permanen"
                                    data-placement="top"
                                    onclick="confirm('{{ __('Delete data permanently ?') }}') ? this.parentElement.submit() : ''">
                                    <span class="feather icon-trash-2"></span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="pull-right">
        {{ $jadwal->appends(Request::all())->links('vendor.pagination.custom') }}
    </div>
</div>
