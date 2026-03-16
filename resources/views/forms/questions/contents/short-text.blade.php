@php
    $isCheck    = ($question->validate_text??null === 1)?'checked':'';
    $isDisable  = ($question->validate_text??null === 1)?'':'disabled';
@endphp

<div class="col-md-12 option-item">
    <div class="input-group">
        <div class="input-group-text">
            <input 
                class="form-check-input mt-0"
                data-bs-toggle="tooltip" data-bs-placement="top" title="Validate text"
                onclick="toggleIsValidateText(this);"
                type="checkbox"
                name="validate_text"
                value="1" {{ $isCheck }}>
        </div>
        <input type="text" class="form-control" name="short_answer" minlength="0" maxlength="191" value="{{ $question->short_answer??null }}" {{ $isDisable }} placeholder="">
    </div>
</div>
