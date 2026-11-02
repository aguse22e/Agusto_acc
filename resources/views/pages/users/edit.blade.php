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
                    <h5>Edit User</h5>
                </div>
                <div class="card-block">
                    {{-- <h4 class="sub-title">Basic Inputs</h4> --}}
                    @include('pages.users._form-edit')
                </div>
            </div>
        </div>
    </div>
@endsection
