<a href="javascript:void(0);"
    @if ($opInd === 1)
        onclick="addOption({{ $question->id }}, {{ $option->id }})"
    @else
        onclick="removeOption({{ $question->id }}, {{ $option->id }})"
    @endif
    >
    <i class="mdi {{ $icon }} fs-24 align-self-center"></i>
</a>