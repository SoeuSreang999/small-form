@php
    $seeting            = $question->QuestionSetting;
    $isMatching         = $seeting->isMatching();
    $htmlEditor         = $question->content ?? null;
@endphp

<div class="row" id="question-item{{ $question->id }}">
    <div class="col-lg-12 mt-2 mb-2 px-3">
        <div class="col-md-9">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="is_matching" id="inlineRadioFreeText{{ $question->id }}" {{ ($isMatching === false)?'checked':null }} value="0">
                <label class="form-check-label" for="inlineRadioFreeText{{ $question->id }}">Free Text (Typing)</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="is_matching" id="inlineRadioMatching{{ $question->id }}" {{ ($isMatching === true)?'checked':null }} value="1">
                <label class="form-check-label" for="inlineRadioMatching{{ $question->id }}">Matching (Drag Drop)</label>
            </div>
        </div>
    </div>

    <div class="col-lg-12 px-3">
        <textarea name="contents" class="form-control gap-textarea mb-3 d-none" rows="4" cols="100">{!! $htmlEditor !!}</textarea>
        <div class="form-control gap-editor" contenteditable="true"
            data-question-id="{{ $question->id }}"
            style="min-height:70px; resize: vertical; overflow: auto;">
        </div>
    </div>

    <div class="col-lg-12 mb-2 px-3">
        <div class="button-items">
            <button type="button" class="btn px-2 pt-2" onclick="addGap(this);">
                <i class="mdi mdi-tag-plus-outline fs-5"></i>
                Add Gap
            </button>
            <button type="button" class="btn px-2 pt-2" onclick="removeGap(this);">
                <i class="mdi mdi-tag-text-outline fs-5"></i>
                Remove Gap
            </button>
            <button type="button" class="btn px-2 pt-2" onclick="clearStyleGap(this);">
                <i class="mdi mdi-tag-remove-outline fs-5"></i>
                Clear Formatting
            </button>
        </div>
    </div>
</div>
