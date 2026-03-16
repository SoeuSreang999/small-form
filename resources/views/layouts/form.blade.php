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
        <link rel="stylesheet" href="{{ asset('customize/form.css') }}">

        @vite(['resources/css/app.css','resources/js/app.js'])
        @stack('style')
    </head>
    <body>
        <div class="page-loader-wrapper">
            <div class="loader">
                <div class="spinner-border text-primary ms-auto" role="status" aria-hidden="true"></div>
            </div>
        </div>
        @include('layouts.topbar')
        <div class="form-wrapper">
            @yield('content')
        </div>
        <modal-element class="modal-element" id="modal-element"></modal-element>
        
        <script>
            window.trans = {
                confirm_delete: @json(__('messages.confirm_delete')),
                confirm_text: @json(__('messages.confirm_text')),
                confirm_yes: @json(__('messages.confirm_yes')),
                confirm_no: @json(__('messages.confirm_no')),
            };
        </script>
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
        <script src="{{ asset('backend/js/pages/animation.init.js') }}"></script>
        <script src="{{ asset('plugins/dropify/js/dropify.min.js') }}"></script>
        {{-- <script src="{{ asset('https://cdn.tiny.cloud/1/t4d9xi38xxeu3wstcte1b7jrnwg5za31wgffugdsvhhje4mr/tinymce/5/tinymce.min.js') }}" referrerpolicy="origin"></script> --}}
        <script src="{{ asset('plugins/tinymce5/js/tinymce/tinymce.min.js') }}"></script>
        <script src="{{ asset('customize/script.js') }}"></script>
        @stack('scripts')
    </body>
</html>
