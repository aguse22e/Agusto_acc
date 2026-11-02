@push('custom-styles')

    <link rel="stylesheet" type="text/css" href="{{ asset('') }}css/themify-icons.css">

    <link rel="stylesheet" type="text/css" href="{{ asset('') }}css/icofont.css">

    <link rel="stylesheet" type="text/css" href="{{ asset('') }}css/font-awesome.min.css">

    <link rel="stylesheet" type="text/css" href="{{ asset('') }}css/notification.css">

    <link rel="stylesheet" type="text/css" href="{{ asset('') }}css/animate.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('') }}css/pages.css">
@endpush

@if (session('status'))
    <div class="alert alert-success background-success">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <i class="icofont icofont-close-line-circled text-white"></i>
        </button>
        <strong>{{ session('status') }}</strong>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger background-danger">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <i class="icofont icofont-close-line-circled text-white"></i>
        </button>
        <strong>{{ session('error') }}</strong>
    </div>
@endif

@push('custom-scripts')
    <script type="bc9e5e682d42f376717182ab-text/javascript" src="{{ asset('') }}js/modernizr.js"></script>
    <script type="bc9e5e682d42f376717182ab-text/javascript" src="{{ asset('') }}js/css-scrollbars.js"></script>

    <script type="bc9e5e682d42f376717182ab-text/javascript" src="{{ asset('') }}js/bootstrap-growl.min.js"></script>
    <script type="bc9e5e682d42f376717182ab-text/javascript" src="{{ asset('') }}js/notification.js"></script>
    <script src="{{ asset('') }}js/pcoded.min.js" type="bc9e5e682d42f376717182ab-text/javascript"></script>
    <script src="{{ asset('') }}js/vertical-layout.min.js" type="bc9e5e682d42f376717182ab-text/javascript"></script>
    <script src="{{ asset('') }}js/jquery.mcustomscrollbar.concat.min.js"
        type="bc9e5e682d42f376717182ab-text/javascript"></script>

    <script type="bc9e5e682d42f376717182ab-text/javascript" src="{{ asset('') }}js/script.js"></script>
@endpush
