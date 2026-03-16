@php
    $locale = app()->getLocale();
@endphp

<div class="col-12">
    <div class="row g-3 align-items-start">
        <div class="col">
            @foreach (($sentences ?? collect([])) as $opInd => $sentence)
                @php
                    $opInd              += 1;
                    $sentenceImageHtml  = $sentence->optionElImage();
                    $noInd              = $locale === 'kh' ? numberToKhmerLetters($opInd) : numberToEnLetters($opInd);
                @endphp
                <div class="mb-2 d-flex align-items-stretch">
                    <div class="mx-2 p-2 w-100 border rounded">
                        <div class="d-flex align-items-start">
                            <span class="me-1">{{ $noInd.'.' }}</span>
                            <span class="fs-14">{{ $sentence->name ?? null }}</span>
                        </div>

                        @if (!empty($sentenceImageHtml))
                            <div class="p-2 ps-0">
                                {!! $sentenceImageHtml !!}
                            </div>
                        @endif
                    </div>

                    <div class="box d-flex align-items-center">
                        <span><i class="mdi mdi-trending-neutral fs-4"></i></span>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="col" data-answer-list="true">
            @foreach (($answers ?? collect([])) as $answer)
                @php
                    $answerImageHtml = $answer->optionElImage();
                @endphp

                <div class="card mb-2 matching-answer-card" data-move-item="true" data-answer-id="{{ $answer->id }}">
                    <div class="body p-2 d-flex align-items-center gap-2" style="cursor: grab; user-select: none;">
                        <i class="mdi mdi-cursor-move"></i>
                        <span class="fs-14">{{ $answer->name ?? null }}</span>
                    </div>

                    @if (!empty($answerImageHtml))
                        <div class="p-2 ps-4">
                            {!! $answerImageHtml !!}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
