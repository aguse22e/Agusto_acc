<div class="table-responsive">
    <table class="table table-styling table-de">
        <thead>
            <tr class="table-primary">
                <th class="text-center">#</th>
                <th>Kode</th>
                <th>Nama</th>
                <th>Kontak Person</th>
                <th>No HP</th>
                <th>Kota</th>
                <th class="text-center">Termin (hari)</th>
                <th class="text-right">Saldo Awal Piutang</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $page = Request::get('page');
                $no = !$page || $page == 1 ? 1 : ($page - 1) * 10 + 1;
            @endphp
            @foreach ($customer as $item)
                <tr class="border-bottom-primary">
                    <td class="text-center text-muted">{{ $no }}</td>
                    <td>{{ $item->kode_customer }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->kontak_person }}</td>
                    <td>{{ $item->no_hp }}</td>
                    <td>{{ $item->kota }}</td>
                    <td class="text-center">{{ $item->termin_hari }}</td>
                    <td class="text-right">{{ number_format($item->piutang, 0, ',', '.') }}</td>
                    <td>
                        <div class="form-inline">
                            <a href="{{ route('customer.show', $item->id) }}" class="mr-2">
                                <button type="button" class="btn btn-info btn-sm"
                                    data-toggle="tooltip" title="Lihat" data-placement="top"><span
                                        class="feather icon-eye"></span></button>
                            </a>
                            <a href="{{ route('customer.edit', $item->id) }}" class="mr-2">
                                <button type="button" id="PopoverCustomT-1" class="btn btn-primary btn-sm"
                                    data-toggle="tooltip" title="Edit" data-placement="top"><span
                                        class="feather icon-edit"></span></button>
                            </a>
                            <form action="{{ route('customer.destroy', $item->id) }}" method="post">
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
        {{ $customer->appends(Request::all())->links('vendor.pagination.custom') }}
    </div>
</div>
