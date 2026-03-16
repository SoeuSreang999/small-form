@extends('layouts.app')
@section('title', __('menu.forms'))
@push('style')
    
@endpush
@section('content')
    <div
        data-vue-form-list
        data-forms='@json($forms->map(fn ($form) => ["uuid" => $form->uuid, "name" => $form->name])->values())'
        data-create-url="{{ route('forms.create') }}"
        data-edit-base-url="{{ url('/forms') }}"
        data-labels='@json([
            "create" => __("action.create") . " " . __("action.new"),
            "search" => __("action.search"),
            "empty" => "No forms found.",
            "edit" => __("action.edit"),
            "name" => "Name",
            "ext" => "Ext.",
            "city" => "City",
            "startDate" => "Start Date",
            "completion" => "Completion",
        ])'
    ></div>
@endsection
