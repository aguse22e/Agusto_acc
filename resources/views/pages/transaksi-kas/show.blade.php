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
            <form method="POST" action="{{ route('transaksiKas.koreksi', $transaksi_kas->kode_transaksi_kas) }}" onsubmit="return confirm('Buat Jurnal Koreksi atas transaksi ini? Jurnal asal tidak diubah dan pembetulan tercatat sebagai memorial baru.')" style="margin-bottom: 10px;">
                @csrf
                <button type="submit" class="btn btn-warning btn-sm"><i class="feather icon-rotate-ccw"></i> Jurnal Koreksi</button>
            </form>
            <div class="card">
                <div class="card-header">
                    <h5>Transaksi Kas</h5>
                </div>
                <div class="card-block">
                    {{-- <h4 class="sub-title">Basic Inputs</h4> --}}
                    @include('pages.transaksi-kas._form-show')
                </div>
            </div>
        </div>
    </div>
@endsection
