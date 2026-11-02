@extends('layouts.template')

@section('page-header')
    @include('components.page-header', [
    'pageTitle' => $pageTitle,
    'pageSubtitle' => '',
    'pageIcon' => $pageIcon,
    'parentMenu' => $parentMenu,
    'current' => $current
    ])
@endsection

@section('content')

    @include('components.notification')

    <div class="row">
        <div class="col-sm-12">
            @include('components.button-list', ['btnText' => $btnText, 'btnLink' => $btnLink])
            <div class="card">
                <div class="card-header">
                    <h5>Detail User Activity</h5>
                </div>
                <div class="card-block">
                    <div class="table-responsive">
                        <table class="table table-styling">
                            <tbody>
                                <tr>
                                    <td style="width: 220px"><strong>User</strong></td>
                                    <td>{{ $activity->name }}{{ $activity->email ? ' (' . $activity->email . ')' : '' }}{{ $activity->level ? ' — ' . $activity->level : '' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Jenis Transaksi</strong></td>
                                    <td>{{ $activity->jenis_transaksi }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tipe</strong></td>
                                    <td>{{ $activity->tipe }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Keterangan</strong></td>
                                    <td>{{ $activity->keterangan }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Waktu</strong></td>
                                    <td>{{ date('d-m-Y H:i:s', strtotime($activity->created_at)) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="text-muted mb-0">Catatan aktivitas adalah jejak audit dan bersifat read-only — tidak dapat diubah atau dihapus dari halaman ini.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
