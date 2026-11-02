@extends('layouts.template')

@section('page-header')
    @include('components.page-header', [
    'pageTitle' => 'Ganti Password',
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
            <div class="card">
                <div class="card-header">
                    <h5>Ganti Password</h5>
                </div>
                <div class="card-block">
                    {{-- <h4 class="sub-title">Basic Inputs</h4> --}}
                    @include('pages.users._form-change-password')
                </div>
            </div>
        </div>
    </div>
@endsection
