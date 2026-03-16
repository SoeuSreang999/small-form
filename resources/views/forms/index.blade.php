@extends('layouts.app')
@section('title', __('menu.forms'))
@push('style')
    
@endpush
@section('content')
    <div class="row">
        <div class="col-sm-10 mx-auto">
            <div class="row">
                <div class="col-lg-3 mb-3">
                    <button class="btn btn-primary" type="button" onclick="createForm();">
                        <i class="fas fa-plus"></i>
                        @lang('action.create') @lang('action.new')
                    </button>
                </div>
                <div class="col-lg-9 float-end">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Search" aria-label="Recipient's username" aria-describedby="button-addon2">
                        <button class="btn btn-primary" type="button" id="button-addon2"><i class="fas fa-search"></i> @lang('action.search')</button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Ext.</th>
                                    <th>City</th>
                                    <th data-type="date" data-format="YYYY/DD/MM">Start Date</th>
                                    <th>Completion</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($forms as $form)
                                    <tr>
                                        <td>{{ $form->name??null }}</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <a href="{{ route('forms.index', $form->uuid) }}">
                                                <span>Edit</span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('forms.script_form')
@endpush
