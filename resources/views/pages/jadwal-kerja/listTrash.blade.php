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

    @include('components.button-add', ['btnText' => $btnText, 'btnLink' => $btnLink])

    <div class="card">
        <div class="card-header">
            <h5>Tempat Sampah — Jadwal Kerja</h5>
        </div>
        <div class="card-block table-border-style">
            @include('pages.jadwal-kerja._tabletrash')
        </div>
    </div>
@endsection
