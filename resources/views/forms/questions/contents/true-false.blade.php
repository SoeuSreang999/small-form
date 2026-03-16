@php
    $opInd              = ($opInd + 1);
    $icon               = ($opInd === 1 ? 'mdi-plus-circle-outline text-primary' : 'mdi-close-circle-outline text-danger');
    $optionElImage      = $option->optionElImage();
@endphp

<div class="col-md-12 option-item" id="option-item{{ $option->id }}">
    <div class="row">
        <div class="col-lg-12 mb-2 d-flex justify-content-between align-items-center gap-3">
            <div class="input-group">
                <div class="input-group-text select-true-false">
                    <select class="form-select true-false-option" id="inputGroupSelect{{ $question->id }}" name="option_{{ $question->id }}[{{ $option->id }}]">
                        <option value="1" {{ ($option->is_correct === 1)?'selected': null }} >True</option>
                        <option value="0" {{ ($option->is_correct === 0)?'selected': null }}>Fase</option>
                    </select>
                </div>
                <input type="text" class="form-control" aria-label="" name="answers[{{ $option->id }}]" value="{{ $option->name_en??null }}" placeholder="">
                <span class="input-group-text cursor-pointer"
                        onclick="optionBrowseImage({{ $option->id }})"
                    >
                    <i class="mdi mdi-folder-image fs-4"></i>
                </span>
            </div>
            @include('forms/questions/contents/options/action')
        </div>
        @include('forms/questions/contents/options/option_file')
    </div>
</div>