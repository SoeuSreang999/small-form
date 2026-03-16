@extends('layouts.form')
@section('title', $form->name??null)
@push('style')

@endpush
@section('content')
    <div class="row">
        <div class="col-xl-6 col-lg-6 col-md-10 col-ms-10 mx-auto" id="form">
            @php
                $sections = $form->sections;
            @endphp
            @foreach ($sections as $secInd => $section)
                @php
                    $questions = $section->questions;
                @endphp
                <div class="list-section-item" id="list-section{{ $section->id }}">
                    @include('forms.views.sections.section')
                    @foreach ($questions as $qIndex => $question)
                        @include('forms.views.questions.question', ['index' => $qIndex])
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
@endsection

@push('scripts')
    @include('forms.views.script_preview')
    @if (Route::is('forms.exam.index'))
        @include('forms.views.script_exam')
    @endif
@endpush
