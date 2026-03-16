@php
    $index                      = ($opInd ?? 0) + 1;
    $optionImageHtml            = $option->optionElImage();
    $option->option_index       = $index;
@endphp

<div class="col-12 mb-2">
    <div class="form-check d-flex align-items-center justify-content-start gap-2">
        <input
            class="form-check-input"
            type="radio"
            name="answers[{{ $question->id }}]"
            id="question{{ $question->id }}_option{{ $option->id }}"
            value="{{ $option->id }}"
        >
        {!! $option->label_name !!}
    </div>

    @if (!empty($optionImageHtml))
        <div class="ms-4 mt-1">
            {!! $optionImageHtml !!}
        </div>
    @endif
</div>
