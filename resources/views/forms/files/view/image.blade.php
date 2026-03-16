<div class="col-12 mb-2 file-item"
    data-file-for="{{ ($file->section_id == null)?'question':'section' }}"
    data-section-id="{{ $file->section_id??null }}"
    data-question-id="{{ $file->question_id??null }}"
    data-file-id="{{ $file->id }}"
>
    <div class="position-relative d-inline-block">
        <img
            class="option-thumb-xxl rounded cursor-pointer"
            src="{{ $file->thumbnail_image ?: asset(Storage::url($file->path)) }}"
            width="200"
            alt="{{ $file->name ?? 'image' }}"
        >
        <span class="position-absolute top-0 start-100 translate-middle rounded-circle bg-white cursor-pointer btn-item-remove"
            onclick="formDeleteFile(this, {{ $file->id }});">
            <small class="thumb-xs">
                <i class="fas fa-trash fw-bold fs-10 text-danger"></i>
            </small>
        </span>
    </div>
</div>
