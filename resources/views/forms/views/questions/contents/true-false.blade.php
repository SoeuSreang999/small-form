@php
    $optionImageHtml    = $option->optionElImage();
    $locale             = app()->getLocale();
    $opInd              = ($opInd??1) + 1;
    $index              = $locale === 'kh' ? numberToKhmerLetters($opInd) : numberToEnLetters($opInd);
@endphp

<div class="col-12 mb-3">
    <div class="row mb-2">
        <div class="col-md-12">
            <h5 class="m-0 align-self-center">{{ $index }}. {{ $option->name ?? '' }}</h5>
            @if (!empty($optionImageHtml))
                <div class="p-2 ps-2">
                    {!! $optionImageHtml !!}
                </div>
            @endif
        </div>
    </div>
    <div class="row px-3">
        <div class="col-md-12">
            <div class="form-check d-flex align-items-center justify-content-start gap-2">
                <input class="form-check-input" type="radio" name="answers[{{ $option->id }}]" id="inlineRadioTrue{{ $question->id??null }}{{ $option->id??null }}" value="1">
                <label class="form-check-label text-body fs-5" for="inlineRadioTrue{{ $question->id??null }}{{ $option->id??null }}">@lang('general.true')</label>
            </div>
            <div class="form-check d-flex align-items-center justify-content-start gap-2">
                <input class="form-check-input" type="radio" name="answers[{{ $option->id }}]" id="inlineRadioFalse{{ $question->id??null }}{{ $option->id??null }}" value="0">
                <label class="form-check-label text-body fs-5" for="inlineRadioFalse{{ $question->id??null }}{{ $option->id??null }}">@lang('general.false')</label>
            </div>
        </div>
    </div>
</div>
