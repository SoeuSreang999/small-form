@php
    $assignPointChecked     = ($question->assign_point??null === 1)?'checked':'';
    $isDisableAssignPoint   = ($question->assign_point??null === 1)?'':'disabled';
    $isInstruction          = (($question->question_type ?? 'question') === 'instruction');
@endphp

<form action="{{ route('forms.questions.update', $question->id) }}" method="POST">
    <div class="card item question-item" onclick="questionClick(this);"
            data-question-id="{{ $question->id }}"
            data-form-id="{{ $question->form_id }}"
            data-section-id="{{ $question->section_id }}"
            data-question-type="{{ $question->question_type ?? 'question' }}"
        >
        <div class="bleft"></div>
        <div class="row g-0">
            <div class="col-md-12">
                <div class="card-body pt-0 pb-1">
                    @if ($isInstruction)
                        @include('forms.questions.instruction', ['question' => $question])
                    @else
                        <div class="row">
                            <div class="col-12 text-center position-relative question-move-item">
                                <i class="mdi mdi-drag-horizontal text-muted-2 fs-3"></i>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-2 gap-3 d-flex justify-content-center align-items-center">
                                <div class="col-auto">
                                    <a href="javascript:void(0);" class="text-muted-2 px-2"
                                        onclick="questionAttachImage({{ $question->id }});"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="Attach Image">
                                        <i class="mdi mdi-paperclip fs-4"></i>
                                    </a>

                                    <a href="javascript:void(0);" class="text-muted-2 px-2"
                                        onclick="questionVoice(this);"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="Voice Input">
                                        <i class="mdi mdi-microphone fs-4"></i>
                                    </a>
                                </div>
                                <div class="col">
                                    <div class="input-group">
                                        <div class="input-group-text" data-bs-toggle="tooltip" data-bs-placement="top" title="Assign Point">
                                            <input class="form-check-input mt-0" type="checkbox" name="assign_point" {{ $assignPointChecked }} onclick="toggleAssignPoint(this);" value="1">
                                        </div>
                                        <input type="number" name="point" value="{{ $question->point ?? null }}" {{ $isDisableAssignPoint }} onkeydown="allowNumberDecimal(event)" class="form-control" placeholder="Point">
                                        <span class="input-group-text">Points</span>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <select class="border rounded form-select2" name="type" onchange="questionChangeType(this, {{ $question->id }});">
                                        @foreach ($qans_types as $id => $name)
                                            <option value="{{ $id }}" {{ ($question->type == $id)?'selected':'' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                @if ($isInstruction)
                                    @include('forms.questions.instruction')
                                @else
                                    <input class="form-control form-control-lg mb-2 question-title" type="text" name="name_en" value="{{ $question->name_en ?? null }}" placeholder="Question title">
                                    <input class="form-control mb-2 tinymce-editor" type="text" name="desc_en" value="{{ $question->desc_en ?? null }}" placeholder="Description">
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 question-document" id="question-document{{ $question->id }}">
                                {!! $question->questionFiles() !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 px-3 question-content" id="question-content{{ $question->id }}">
                                @if (!$isInstruction)
                                    {!! $question->content() !!}
                                @endif
                            </div>
                        </div>
                        <div class="row footer-buttom">
                            <div class="col-12">
                                <hr class="my-2">
                                <div class="d-flex justify-content-end align-items-center gap-3">
                                    <a href="javascript:void(0);"
                                        onclick="copyQuestion(this)"
                                        class="text-muted-2 px-2" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-placement="top" 
                                        title="Copy"
                                    >
                                        <i class="mdi mdi-content-copy fs-4"></i>
                                    </a>
                                    <a href="javascript:void(0);"
                                        onclick="deleteQuestion(this)"
                                        class="text-muted-2 px-2" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-placement="top" 
                                        title="Delete"
                                    >
                                        <i class="mdi mdi-delete-outline fs-4"></i>
                                    </a>
                                    <div class="vr"></div>
                                    <div class="form-check form-switch mb-0 form-switch-pink">
                                        <input class="form-check-input" type="checkbox" id="required{{ $question->id }}" name="required" {{ ($question->required == 1)?'checked':'' }} value="1">
                                        <label class="form-check-label" for="required{{ $question->id }}">
                                            Required
                                        </label>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" id="option_shuffle{{ $question->id }}" name="option_shuffle" {{ ($question->option_shuffle == 1)?'checked':'' }} value="1">
                                        <label class="form-check-label" for="option_shuffle{{ $question->id }}">
                                            Shuffle option answer
                                        </label>
                                    </div>
                                    <a href="javascript:void(0);" class="text-muted-2 px-2"><i class="mdi mdi-dots-vertical fs-4"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="question-option-right">
                                    <a href="javascript:void(0);" class="text-muted-2"
                                        onclick="addQuestion(this)"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        title="Add Question">
                                        <i class="mdi mdi-plus-circle-outline fs-3 font-18 text-primary"></i>
                                    </a>

                                    <a href="javascript:void(0);" class="text-muted-2"
                                        onclick="addInstruction(this)"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        title="Instructions">
                                        <i class="mdi mdi-format-title fs-3 font-18 text-primary"></i>
                                    </a>

                                    <a href="javascript:void(0);" class="text-muted-2"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        title="Import Question">
                                        <i class="mdi mdi-file-import-outline fs-3 font-18 text-primary"></i>
                                    </a>

                                    <a href="javascript:void(0);" class="text-muted-2"
                                        onclick="addSection(this)"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        title="Add Section">
                                        <i class="mdi mdi-content-paste fs-3 font-18 text-primary"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</form>
