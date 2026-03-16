@php
    $opInd          = $opInd + 1;
    $icon           = $opInd === 1
        ? 'mdi-plus-circle-outline text-primary'
        : 'mdi-close-circle-outline text-danger';

    $answer = $option->answerMatching;

    $optionImageHtml = $option->optionElImage();
    $answerImageHtml = $answer?->optionElImage();

    $optionHasImage = !empty($optionImageHtml);
    $answerHasImage = !empty($answerImageHtml);
@endphp

@if ($opInd === 1)
    <div class="row mt-2 mb-2">
        <div class="col-md-6 text-center">
            <span>The Sentence</span>
        </div>
        <div class="col-md-6 text-center">
            <span>The Answer</span>
        </div>
    </div>
@endif

<div class="col-12 option-item"
     id="option-item{{ $option->id }}"
     data-option-id="{{ $option->id }}">
    <div class="row">
        <div class="col-12 mb-2 d-flex justify-content-between align-items-start gap-3 flex-wrap">
            <div class="flex-grow-1">
                <label for="sentence-{{ $option->id }}" class="form-label visually-hidden">Sentence</label>
                <div class="input-group">
                    <input
                        id="sentence-{{ $option->id }}"
                        type="text"
                        class="form-control"
                        name="sentences[{{ $option->id }}]"
                        value="{{ $option->name_en }}"
                        placeholder="Type sentence..."
                    >
                    <button
                        type="button"
                        class="input-group-text cursor-pointer"
                        onclick="optionBrowseImage({{ $option->id }})"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Add Image"
                    >
                        <i class="mdi mdi-folder-image fs-4"></i>
                    </button>
                </div>

                <div id="option-file{{ $option->id }}"
                     class="option-file d-flex justify-content-between align-items-center gap-3 {{ $optionHasImage ? 'mt-2' : 'mt-0' }}">
                    {!! $optionImageHtml !!}
                </div>
            </div>
            <span class="pt-2"><i class="mdi mdi-trending-neutral fs-4"></i></span>
            <div class="flex-grow-1">
                <label for="answer-{{ $answer?->id }}" class="form-label visually-hidden">Answer</label>
                <div class="input-group">
                    <input
                        id="answer-{{ $answer?->id }}"
                        type="text"
                        class="form-control"
                        name="answers[{{ $answer?->id }}]"
                        value="{{ $answer?->name_en }}"
                        placeholder="Type answer..."
                    >
                    <button
                        type="button"
                        class="input-group-text cursor-pointer"
                        onclick="optionBrowseImage({{ $answer?->id }})"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Add Image"
                    >
                        <i class="mdi mdi-folder-image fs-4"></i>
                    </button>
                </div>

                <div id="option-file{{ $answer?->id }}"
                     class="option-file d-flex justify-content-between align-items-center gap-3 {{ $answerHasImage ? 'mt-2' : 'mt-0' }}">
                    {!! $answerImageHtml !!}
                </div>
            </div>
            @include('forms/questions/contents/options/action')
        </div>
    </div>
</div>
