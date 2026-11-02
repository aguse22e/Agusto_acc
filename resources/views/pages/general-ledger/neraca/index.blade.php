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
            <h5>Laporan Posisi Keuangan (Neraca)</h5>
        </div>
        {{-- form filter --}}
        <div class="card-body">
            @include('pages.general-ledger.neraca._form-filter')
        </div>
        {{-- end form filter --}}

        {{-- show neraca --}}
        @if (isset($neraca) && isset($sampai))
        @include('pages.general-ledger.neraca._show-neraca')
        @endif
    </div>
@endsection
