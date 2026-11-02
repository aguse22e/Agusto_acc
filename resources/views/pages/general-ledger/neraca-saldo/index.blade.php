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

    <div class="card">
        <div class="card-header">

            <h5>Neraca Saldo</h5>
            {{-- <div class="col-md-4 pull-right">
                @include('components.search')
            </div> --}}

        </div>
        {{-- form filter --}}
        <div class="card-body">
            @include('pages.general-ledger.neraca-saldo._form-filter')
        </div>
        {{-- end form filter --}}

        {{-- show buku besar --}}
        @if (isset($kodeAkun) && isset($dari) && isset($sampai))
        @include('pages.general-ledger.neraca-saldo._show-neraca-saldo')
        @endif
    </div>
@endsection
