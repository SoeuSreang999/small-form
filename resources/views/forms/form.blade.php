 @extends('layouts.form')
@section('title', $form->name??null)
@push('style')

@endpush
@section('content')
    <div class="row">
        <div class="col-xl-6 col-lg-6 col-md-10 col-ms-10 mx-auto">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tabQuestion" role="tab" url="questions" aria-selected="true">Questions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tabSetting" role="tab" url="settings" aria-selected="false">Settings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tabResporn" role="tab" url="responses" aria-selected="false">Responses</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane px-3 active" id="tabQuestion" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-12 mt-3" id="form" data-form-id="{{ $form->id }}">
                            @php
                                $sections = $form->sections;
                            @endphp
                            @foreach ($sections as $secInd => $section)
                                @php
                                    $questions = $section->questions;
                                @endphp

                                <div class="list-section-item" id="list-section{{ $section->id }}">
                                    @include('forms.sections.section')
                                    @foreach ($questions as $question)
                                        @include('forms.questions.question')
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="tab-pane px-3" id="tabResporn" role="tabpanel">
                    <p class="mb-0 text-muted">
                        Comming soon..............
                    </p>
                </div>
                <div class="tab-pane px-3" id="tabSetting" role="tabpanel">
                    @include('forms.settings.index')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('forms.script_form')
@endpush
