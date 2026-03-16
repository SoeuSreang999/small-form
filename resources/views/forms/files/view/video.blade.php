<div class="col-12 mb-2 file-item position-relative"
        data-file-for="{{ ($file->section_id == null)?'question':'section' }}"
        data-section-id="{{ $file->section_id??null }}"
        data-question-id="{{ $file->question_id??null }}"
        data-file-id="{{ $file->id }}"
    >
    <div class="col-12 text-center position-absolute p-0 m-0" style="top:-8px;left:50%;transform:translateX(-50%)">
        <i class="mdi mdi-drag-horizontal fs-4"></i>
    </div>
    <div class="col-12 border rounded p-2 d-flex align-items-center gap-3">
        <video controls style="max-height:120px; width:auto;">
            <source src="{{ Storage::url($file->path) }}" type="video/{{ strtolower($file->type ?? '') }}">
        </video>
        <div class="flex-grow-1 text-truncate">
            <a href="{{ Storage::url($file->path) }}" target="_blank" class="text-decoration-none">{{ $file->name ?? $file->name_en ?? basename($file->path) }}</a>
            <div class="small text-muted text-uppercase">{{ $file->type }}</div>
        </div>
        <button type="button" class="btn_remove_voice" onclick="formDeleteFile(this, {{ $file->id }});">
            <i class="fas fa-trash text-danger"></i>
        </button>
    </div>
</div>
