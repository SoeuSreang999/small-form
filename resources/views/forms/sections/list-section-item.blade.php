<div class="list-section-item" id="list-section{{ $section->id }}">
    @include('forms.sections.section')

    @if(isset($questions))
        @foreach($questions as $question)
            @include('forms.questions.question')
        @endforeach
    @else
        @include('forms.questions.question')
    @endif
</div>
