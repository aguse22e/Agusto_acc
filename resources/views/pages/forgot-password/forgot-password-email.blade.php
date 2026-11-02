<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from colorlib.com/polygon/admindek/default/auth-sign-in-social.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 12 Dec 2019 16:08:30 GMT -->
<!-- Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->

<head>
    <title>Administrator | Accounting</title>


    <!--[if lt IE 10]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
      <![endif]-->

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description"
        content="Admindek Bootstrap admin template made using Bootstrap 4 and it has huge amount of ready made feature, UI components, pages which completely fulfills any dashboard needs." />
    <meta name="keywords"
        content="bootstrap, bootstrap admin template, admin theme, admin dashboard, dashboard template, admin template, responsive" />
    <meta name="author" content="colorlib" />

    <link rel="icon" href="{{ asset('') }}png/jatim.png" type="image/x-icon">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Quicksand:500,700" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="{{ asset('') }}css/bootstrap.min.css">

    <link rel="stylesheet" href="{{ asset('') }}css/waves.min.css" type="text/css" media="all">
    <link rel="stylesheet" type="text/css" href="{{ asset('') }}css/feather.css">

    <link rel="stylesheet" type="text/css" href="{{ asset('') }}css/themify-icons.css">

    <link rel="stylesheet" type="text/css" href="{{ asset('') }}css/icofont.css">

    <link rel="stylesheet" type="text/css" href="{{ asset('') }}css/font-awesome.min.css">

    <link rel="stylesheet" type="text/css" href="{{ asset('') }}css/style.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('') }}css/pages.css">
</head>

<body themebg-pattern="theme1">

    <div class="theme-loader">
        <div class="loader-track">
            <div class="preloader-wrapper">
                <div class="spinner-layer spinner-blue">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="gap-patch">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
                <div class="spinner-layer spinner-red">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="gap-patch">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
                <div class="spinner-layer spinner-yellow">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="gap-patch">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
                <div class="spinner-layer spinner-green">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="gap-patch">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="login-block">

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">

                    <form class="md-float-material form-material" method="post" class="" action="{{ route('lupa_password_email_process') }}">
                        @method('put')
                        <div class="text-center">
                            {{-- <img src="{{ asset('') }}png/jatim.png" alt="logo.png" style="height:80px">
                            <img src="{{ asset('') }}png/jatim-bangkit.png" alt="jatim bangkit.png" style="height:70px">
                            <br>
                            <br> --}}
                            <h3><strong>Administrator | Accounting</strong></h3>
                        </div>
                        <div class="auth-box card">
                            <div class="card-block">
                                <div class="row m-b-10">
                                    <div class="col-md-12">
                                        <h3 class="text-center txt-primary">Masukkan Email</h3>
                                    </div>
                                </div>

                                @if ($errors->any())

                                    @foreach ($errors->all() as $error)
                                        <div class="alert alert-primary d-flex align-items-center" role="alert">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                fill="currentColor"
                                                class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2"
                                                viewBox="0 0 16 16" role="img" aria-label="Warning:">
                                                <path
                                                    d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                                            </svg>
                                            <div>
                                                {{ $error }}
                                            </div>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <i class="icofont icofont-close-line-circled text-white"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @endif
                                @csrf
                                @if (session('error'))
                                <div class="alert alert-danger background-danger">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <i class="icofont icofont-close-line-circled text-white"></i>
                                    </button>
                                    {{session('error')}}
                                </div>
                                @endif
                                <div class="form-group form-primary">
                                    <input type="email" name="email"
                                        class="form-control" value="{{ old('email') }}"
                                        required autocomplete="email" autofocus>
                                    <span class="form-bar"></span>
                                    <label class="float-label">Email</label>
                                </div>
                                <div class="row m-t-30">
                                    <div class="col-md-12">
                                        <button type="submit"
                                            class="btn btn-primary btn-md btn-block waves-effect text-center m-b-20">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>

            </div>

        </div>

        </div>

    </section>

    <script type="4878d7dfa7bc22a8dfa99416-text/javascript" src="{{ asset('') }}js/jquery.min.js"></script>
    <script type="4878d7dfa7bc22a8dfa99416-text/javascript" src="{{ asset('') }}js/jquery-ui.min.js"></script>
    <script type="4878d7dfa7bc22a8dfa99416-text/javascript" src="{{ asset('') }}js/popper.min.js"></script>
    <script type="4878d7dfa7bc22a8dfa99416-text/javascript" src="{{ asset('') }}js/bootstrap.min.js"></script>

    <script src="{{ asset('') }}js/waves.min.js" type="4878d7dfa7bc22a8dfa99416-text/javascript"></script>

    <script type="4878d7dfa7bc22a8dfa99416-text/javascript" src="{{ asset('') }}js/jquery.slimscroll.js"></script>

    <script type="4878d7dfa7bc22a8dfa99416-text/javascript" src="{{ asset('') }}js/modernizr.js"></script>
    <script type="4878d7dfa7bc22a8dfa99416-text/javascript" src="{{ asset('') }}js/css-scrollbars.js"></script>
    <script type="4878d7dfa7bc22a8dfa99416-text/javascript" src="{{ asset('') }}js/common-pages.js"></script>

    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-23581568-13"
        type="4878d7dfa7bc22a8dfa99416-text/javascript"></script>
    <script type="4878d7dfa7bc22a8dfa99416-text/javascript">
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-23581568-13');
    </script>
    <script src="{{ asset('') }}js/rocket-loader.min.js" data-cf-settings="4878d7dfa7bc22a8dfa99416-|49" defer=""></script>
</body>

<!-- Mirrored from colorlib.com/polygon/admindek/default/auth-sign-in-social.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 12 Dec 2019 16:08:30 GMT -->

</html>
