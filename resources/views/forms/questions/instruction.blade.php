<div class="col-lg-12">
    <div class="row">
        <div class="col-12 text-center position-relative question-move-item">
            <i class="mdi mdi-drag-horizontal text-muted-2 fs-3"></i>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <input
                class="form-control form-control-lg mb-2 question-title"
                type="text"
                name="name_en"
                value="{{ $question->name_en ?? null }}"
                placeholder="Instruction title"
            >
            <input
                class="form-control mb-2 tinymce-editor"
                type="text"
                name="desc_en"
                value="{{ $question->desc_en ?? null }}"
                placeholder="Type instructions"
            >
        </div>
    </div>
    <div class="row footer-buttom">
        <div class="col mt-2">
            <div class="px-3">
                <div class="mb-2">
                    <h4 class="">Document Instruction</h4>
                    <p class="start-recording-text">Click to record audio using your microphone</p>
                </div>
                <div class="mb-2">
                    <div class="button-items mt-3">
                        <a href="javascript:void(0);"
                            class="btn btn-sm py-0 btn-outline-secondary active"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="Browse File"
                        >
                            <i class="mdi mdi-paperclip fs-4"></i>
                        </a>
                        <a href="javascript:void(0);"
                            class="btn btn-sm py-0 btn-outline-primary"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="Browse PC"
                        >
                            <i class="mdi mdi-upload fs-4"></i>
                        </a>
                        <a href="javascript:void(0);"
                            onclick="sectionVoice(this);"
                            class="btn btn-sm py-0 btn-outline-pink voice"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="Voice"
                        >
                            <i class="mdi mdi-microphone fs-4"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-auto mt-2">
            <div class="mb-2 px-3 d-flex gap-3">
                <div class="tracking-date fs-3">
                    <p class="p-0 m-0" id="rec-hh">00</p>
                    <span class="d-block fs-12 text-muted text-center">HH</span>
                </div>
                <div class="tracking-date fs-3">
                    <p class="p-0 m-0" id="rec-mm">00</p>
                    <span class="d-block fs-12 text-muted text-center">MM</span>
                </div>
                <div class="tracking-date fs-3">
                    <p class="p-0 m-0" id="rec-ss">00</p>
                    <span class="d-block fs-12 text-muted text-center">SS</span>
                </div>
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
                    onclick="deleteInstruction(this)"
                    data-bs-toggle="tooltip"
                    data-bs-placement="right"
                    title="Delete Instruction">
                    <i class="mdi mdi-delete-outline fs-3 font-18 text-primary"></i>
                </a>
            </div>
        </div>
    </div>
</div>
