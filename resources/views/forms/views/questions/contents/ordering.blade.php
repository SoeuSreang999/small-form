@php
    $index                      = ($opInd ?? 0) + 1;
    $optionImageHtml            = $option->optionElImage();
    $option->option_index       = $index;
    $locale                     = app()->getLocale();
    $noInd                      = $locale === 'kh' ? numberToKhmerLetters($index) : numberToEnLetters($index);
@endphp

<div class="col-12 card mb-2 ordering-answer-card" data-move-item="true" data-option-id="{{ $option->id }}">
    <div class="body p-2 d-flex align-items-center gap-1" style="cursor: grab; user-select: none;">
        <i class="mdi mdi-cursor-move me-1"></i>
        <span class="fs-14" index-order="{{ $noInd }}">{{ $noInd.'. ' }}</span>
        <span class="fs-14">{{ $option->name??null }}</span>
    </div>
    @if (!empty($optionImageHtml))
        <div class="p-2 ps-4">
            {!! $optionImageHtml !!}
        </div>
    @endif
</div>
