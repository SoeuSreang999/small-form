@extends('layouts.app')
@section('title', __('menu.classes'))

@section('content')
    <div class="row">
        <div class="col-lg-12 ms-auto d-flex gap-4 align-items-center">
            <div class="col d-flex align-items-center">
                <span class="me-2">{{__('general.rows')}}</span>
                <select class="form-select border rounded w-auto select-page-length form-select2">
                    @php($itemRows = config('app.table.item'))
                    @foreach ($itemRows as $item)
                        <option value="{{ $item }}">{{ $item }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto d-flex align-items-center">
                <button class="btn btn-outline-primary" onclick="addClasses();">
                    <i class="fas fa-plus me-1"></i>
                    <span class="">{{ __('action.class.add')}}</span>
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            {!! $dataTable->table() !!}
            {!! $dataTable->scripts() !!}
        </div>
    </div>
@endsection

@push('scripts')
    @include('classes.script')
@endpush
