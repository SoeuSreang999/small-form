<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr" data-startbar="light" data-bs-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="shortcut icon" href="{{ asset('backend/images/logo-sm.ico') }}">
        <title>{{ config('app.name') }} | @yield('title')</title>
        <link rel="stylesheet" href="{{ asset('backend/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('backend/css/icons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('backend/css/app.min.css') }}">
        <link rel="stylesheet" href="{{ asset('backend/datatables/dataTables.bootstrap5.min.css') }}">
        <link rel="stylesheet" href="{{ asset('backend/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('backend/libs/animate.css/animate.min.css') }}">
        <link rel="stylesheet" href="{{ asset('backend/flatpickr/flatpickr.min.css') }}">
        <link rel="stylesheet" href="{{ asset('backend/daterangepicker/daterangepicker.css') }}">
        <link rel="stylesheet" href="{{ asset('backend/select2/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('backend/notyf/notyf.min.css') }}">
        <link rel="stylesheet" href="{{ asset('backend/libs/mobius1-selectr/selectr.min.css') }}"/>
        <link rel="stylesheet" href="{{ asset('plugins/dropify/css/dropify.min.css') }}"/>
        <link rel="stylesheet" href="{{ asset('customize/style.css') }}">

        @vite(['resources/js/app.js'])
        @stack('style')
    </head>
    <body>
        <div class="page-loader-wrapper">
            <div class="loader">
                <div class="spinner-border text-primary ms-auto" role="status" aria-hidden="true"></div>
            </div>
        </div>

        @include('layouts.topbar')
        @include('layouts.startbar')

        <div class="page-wrapper">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12 mb-2">
                            <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                                <h4 class="page-title mb-1">@yield('title')</h4>
                                {!! Breadcrumbs::render() !!}
                            </div>
                        </div>
                    </div>
                    @yield('content')
                </div>
            </div>
        </div>
        <modal-element class="modal-element" id="modal-element"></modal-element>
        
        <script src="{{ asset('backend/js/jquery.min.js') }}"></script>
        <script src="{{ asset('backend/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('backend/libs/simplebar/simplebar.min.js') }}"></script>
        <script src="{{ asset('backend/js/app.js') }}"></script>
        <script src="{{ asset('backend/datatables/dataTables.min.js') }}"></script>
        <script src="{{ asset('backend/datatables/dataTables.bootstrap5.min.js') }}"></script>
        <script src="{{ asset('backend/libs/sweetalert2/sweetalert2.min.js') }}"></script>
        <script src="{{ asset('backend/js/pages/sweet-alert.init.js') }}"></script>
        <script src="{{ asset('backend/flatpickr/flatpickr.min.js') }}"></script>
        <script src="{{ asset('backend/daterangepicker/moment.min.js') }}"></script>
        <script src="{{ asset('backend/daterangepicker/daterangepicker.min.js') }}"></script>
        <script src="{{ asset('backend/select2/select2.min.js') }}"></script>
        <script src="{{ asset('backend/notyf/notyf.min.js') }}"></script>
        <script src="{{ asset('backend/libs/mobius1-selectr/selectr.min.js') }}"></script>
        <script src="{{ asset('plugins/dropify/js/dropify.min.js') }}"></script>
        <script src="{{ asset('customize/script.js') }}"></script>

        @stack('scripts')
    </body>
</html>