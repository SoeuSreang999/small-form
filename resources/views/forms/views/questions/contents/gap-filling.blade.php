@php
    use App\Models\Form\Questions;

    $isMatching         = $question->QuestionSetting->isMatching();
    $isBoxWord          = $isMatching ?"box-word rounded-1 bg-blue-subtle word-item":"";
    $html               = (string) ($question->content ?? '');
    $html               = preg_replace('/\svalue="[^"]*"/i', '', $html);
    $html               = preg_replace('/name="gaps_\d+\[\]"/i', 'name="answers[' . $question->id . '][]"', $html);
    $html               = preg_replace_callback('/<input\s+([^>]*?)>/i', [Questions::class, 'formatGapPreviewInputTag'], $html);
@endphp

<div class="col-12">
    <small class="form-text text-muted fs-6">
        {{ $isMatching ? 'Drag and drop the words into the blanks.' : 'Fill in the blanks.' }}
    </small>
    <div class="card mb-3">
        <div class="card-body text-center gapping-words d-flex justify-content-center" gapfilling-drag-drop="{{ $isMatching }}">
            @forelse ($options as $option)
                <div class="d-inline-flex align-items-center {{ $isBoxWord }}" id="word-item-{{ $option->id }}" data-word="{{ $option->name??null }}">
                    <span class="mx-2">{{ $option->name??null }}</span>
                </div>
                @if (!$loop->last)
                    <div class="vr align-self-center"></div>
                @endif
            @empty

            @endforelse
        </div>
    </div>
</div>
<div class="col-12 gapfilling-content">
    {!! $html !!}
</div>
