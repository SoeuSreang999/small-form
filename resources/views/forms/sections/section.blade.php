@php
    $secId      = $section->id;
    $secInd     = ($secInd != null)?($secInd + 1):0;
    $secActive  = ($secInd == 1)?'active':'';
    $secFiles   = $section->files;
@endphp

<form action="{{ route('forms.sections.update', $secId) }}" method="POST">
    <div class="card item section-item {{ $secActive }}"
            onclick="sectionClick(this);"
            data-item-id="{{ $secId }}"
            data-section-id="{{ $secId }}"
            data-form-id="{{ $section->form_id }}"
            data-item-type="section"
        >
        <div class="section-header"></div>
        <div class="bleft"></div>
        <div class="row g-0">
            <div class="col-md-12">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 text-center position-relative section-move-item">
                            <i class="mdi mdi-drag-horizontal text-muted-2 fs-3"></i>
                        </div>
                    </div>
                    <input class="form-control form-control-xxl mb-2 section-title" type="text" name="name_en" value="{{ $section->name_en??null }}" oninput="sectionDataChange(this)" placeholder="Section Title">
                    <input class="form-control tinymce-editor" type="text" value="{{ $section->desc_en??null }}" oninput="sectionDataChange(this)" name="desc_en" placeholder="Description">
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
                                            onclick="sectionBrowseFile(this);"
                                            title="Browse File"
                                        >
                                            <i class="mdi mdi-paperclip fs-4"></i>
                                        </a>
                                        <a href="javascript:void(0);"
                                            class="btn btn-sm py-0 btn-outline-primary"
                                            onclick="sectionBrowseFilePC(this);"
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
                        <div class="col-12 mt-2" id="section-document{{ $secId }}">
                            @foreach ($secFiles as $file)
                                @include('forms.files.view.file')
                            @endforeach
                        </div>
                        <div class="section-option-right">
                           <a href="javascript:void(0);" class="text-muted"
                                onclick="addQuestion(this);"
                                data-bs-toggle="tooltip"
                                data-bs-placement="right"
                                title="Add Question">
                                <i class="mdi mdi-plus-circle-outline fs-3 font-18 text-primary"></i>
                            </a>

                            <a href="javascript:void(0);" class="text-muted"
                                onclick="copySection(this);"
                                data-bs-toggle="tooltip"
                                data-bs-placement="right"
                                title="Copy Section">
                                <i class="mdi mdi-content-copy fs-3 font-18 text-primary"></i>
                            </a>

                            <a href="javascript:void(0);" class="text-muted"
                                onclick="deleteSection(this);"
                                data-bs-toggle="tooltip"
                                data-bs-placement="right"
                                title="Remove Section">
                                <i class="mdi mdi-delete-outline fs-3 font-18 text-primary"></i>
                            </a>

                            <a href="javascript:void(0);" class="text-muted"
                                data-bs-toggle="tooltip"
                                data-bs-placement="right"
                                title="Import Question">
                                <i class="mdi mdi-file-import-outline fs-3 font-18 text-primary"></i>
                            </a>

                            <a href="javascript:void(0);" class="text-muted"
                                onclick="addSection(this)"
                                data-bs-toggle="tooltip"
                                data-bs-placement="right"
                                title="Add Section">
                                <i class="mdi mdi-content-paste fs-3 font-18 text-primary"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
