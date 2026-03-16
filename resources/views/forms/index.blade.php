@extends('layouts.app')
@section('title', __('menu.forms'))
@push('style')
    
@endpush
@section('content')
    @php
        $formListProps = [
            'initialItems' => $forms->map(fn ($form) => ['uuid' => $form->uuid, 'name' => $form->name])->values()->all(),
            'labels' => [
                'create' => __('action.create') . ' ' . __('action.new'),
                'search' => __('action.search'),
                'empty' => 'No forms found.',
                'edit' => __('action.edit'),
                'name' => 'Name',
                'ext' => 'Ext.',
                'city' => 'City',
                'startDate' => 'Start Date',
                'completion' => 'Completion',
            ],
        ];
    @endphp

    <div
        data-vue-form-list
        data-create-url="{{ route('forms.create') }}"
        data-edit-base-url="{{ url('/forms') }}"
    >
        <script type="application/json" data-vue-form-list-props>@json($formListProps)</script>
    </div>
@endsection
