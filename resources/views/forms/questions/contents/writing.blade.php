@php
    $isDisable  = ($question->limit_word != 1)?'disabled':'';
    $isCheck    = ($question->limit_word === 1)?'checked':'';
@endphp

<div class="col-md-12 option-item">
    <div class="input-group">
        <span class="input-group-text">
            <input 
                class="form-check-input mt-0 "
                data-bs-toggle="tooltip" data-bs-placement="top" title="Limit words"
                onclick="toggleIsLimitWords(this);"
                type="checkbox"
                id="enableLimitWord{{ $question->id }}"
                name="limit_word"
                {{ $isCheck }}
                value="1"
            >
        </span>
        <input 
            type="number"
            onkeydown="allowNumberOnly(event)"
            class="form-control" 
            min="0" 
            placeholder="Min"
            name="min"
            value="{{ $question->min??null }}"
            {{ $isDisable }}
        >
        <span class="input-group-text">Words To</span>
        <input 
            type="number"
            onkeydown="allowNumberOnly(event)"
            class="form-control" 
            min="0" 
            placeholder="Max"
            name="max"
            value="{{ $question->max??null }}"
            {{ $isDisable }}
        >
        <span class="input-group-text">Words</span>
    </div>
</div>
