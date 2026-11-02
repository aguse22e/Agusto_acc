<div class="table-responsive">
    <table class="table table-styling table-de">
        <thead>
            <tr class="table-primary">
                <th class="text-center">#</th>
                <th>Kode Kas</th>
                <th>Tanggal</th>
                <th>Kode Akun Bank</th>
                <th>Tipe</th>
                <th>Total</th>
                <th>Aksi</th>
                {{-- {!! Auth::user()->level != 'Viewer' ? "" : '' !!} --}}
            </tr>
        </thead>
        <tbody>
            @php
                $page = Request::get('page');
                $no = !$page || $page == 1 ? 1 : ($page - 1) * 10 + 1;
            @endphp
            @foreach ($transaksi_bank as $item)
                <tr class="border-bottom-primary">
                    <td class="text-center text-muted">{{ $no }}</td>
                    <td>{{ $item->kode_transaksi_bank }}</td>
                    <td>{{ date('d-m-Y', strtotime($item->tanggal)) }}</td>
                    <td>{{ $item->akun_kode }}</td>
                    <td>{{ $item->tipe }}</td>
                    <td>Rp. {{number_format($item->total, 2, ',', '.') }}</td>
                    <td>
                        <div class="form-inline">
                                <a href="{{ route('bank-transaksi.show', $item->kode_transaksi_bank) }}" class="mr-2">
                                    <button type="button" id="PopoverCustomT-1" class="btn btn-info btn-sm"
                                    data-toggle="tooltip" title="Detail" data-placement="top"><span
                                    class="feather icon-info"></span></button>
                                </a>
                            @if (Auth::user()->level != 'Viewer')
                                <a href="{{ route('bank-transaksi.edit', $item->kode_transaksi_bank) }}" class="mr-2">
                                    <button type="button" id="PopoverCustomT-1" class="btn btn-primary btn-sm"
                                        data-toggle="tooltip" title="Edit" data-placement="top"><span
                                            class="feather icon-edit"></span></button>
                                </a>
                                <form action="{{ route('bank-transaksi.destroy', $item->kode_transaksi_bank) }}" method="post">
                                    @csrf
                                    @method('delete')
                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Hapus"
                                        data-placement="top"
                                        onclick="confirm('{{ __('Move data to trash ?') }}') ? this.parentElement.submit() : ''">
                                        <span class="feather icon-trash"></span>
                                    </button>
                                </form>
                            @endif
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
        {{ $transaksi_bank->appends(Request::all())->links('vendor.pagination.custom') }}
    </div>
</div>
