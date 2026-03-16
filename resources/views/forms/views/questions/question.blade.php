@php
    $qIndex                         = ($qIndex ?? 0) + 1;
    $question->question_index       = $qIndex;
    $quesfiles                      = $question->files;
    $isAssignPoint                  = $question->isAssignPoint();
    $assignPointChecked             = ($isAssignPoint)?'checked':'';
    $isDisableAssignPoint           = ($isAssignPoint)?'':'disabled';
    $isInstruction                  = $question->isInstruction();
    $isRequired                     = $question->isRequired();
    $requiredEl                     = ($isRequired)?'<span class="text-danger">*</span>':'';
    $assignPointEl                  = ($isAssignPoint)?'<span class="">('.$question->point.' points)</span>':'';
@endphp

<form action="{{ route('forms.exam.question.store', $question->id) }}" method="POST" onchange="examQuestionAutoSave(this)">
    <div class="card item question-item"
            data-question-id="{{ $question->id }}"
            data-form-id="{{ $question->form_id }}"
            data-section-id="{{ $question->section_id }}"
            data-question-type="{{ $question->question_type ?? 'question' }}"
        >
        <div class="row g-0">
            <div class="col-md-12">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h5 class="mb-2">{!! $question->name_label !!}</h5>
                            <h6 class="text-muted">{!! $question->desc_en ?? '' !!}</h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 px-3" id="content-question-{{ $question->id }}">
                            {!! $question->previewContent() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
