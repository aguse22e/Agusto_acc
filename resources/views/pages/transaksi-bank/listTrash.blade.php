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

    @if (Auth::user()->level != 'Viewer')
    @include('components.button-add', ['btnText' => $btnText, 'btnLink' => $btnLink])
    @endif

    <ul class="nav nav-tabs">
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('bank-transaksi.index') ? 'active' : '' }}" href="{{ url('/bank/bank-transaksi') }}">List Transaksi Bank</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Request::segment(3) == 'trash' ? 'active' : '' }}" href="{{ url('/bank/bank-transaksi/trash') }}">Tempat Sampah</a>
        </li>
    </ul>
    <div class="card">
        <div class="card-header">
            <h5>Tempat Sampah</h5>
            <div class="col-md-4 pull-right">
                @include('components.search')
            </div>

        </div>
        <div class="card-block table-border-style">
            @include('pages.transaksi-bank._tabletrash')
        </div>
    </div>
@endsection
