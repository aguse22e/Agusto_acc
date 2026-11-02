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
            <h5>Laporan Arus Kas</h5>
        </div>
        <div class="card-body">
            @include('pages.general-ledger.arus-kas._form-filter')
        </div>

        @if (isset($arusKas) && isset($dari) && isset($sampai))
        @include('pages.general-ledger.arus-kas._show-arus-kas')
        @endif
    </div>
@endsection
