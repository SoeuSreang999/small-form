<div class="col-12 mb-2 file-item audio-file-item position-relative"
        data-file-for="{{ ($file->section_id == null)?'question':'section' }}"
        data-section-id="{{ $file->section_id??null }}"
        data-question-id="{{ $file->question_id??null }}"
        data-file-id="{{ $file->id }}"
    >
    <div class="col-12 text-center position-absolute p-0 m-0" style="top:-8px;left:50%;transform:translateX(-50%)">
        <i class="mdi mdi-drag-horizontal fs-4"></i>
    </div>
    <div class="col-12">
        <div class="voice-message-player">
            <button type="button" class="play-pause-btn">
                <i class="mdi mdi-play"></i>
            </button>
            <div class="waveform-container" data-bars="50"></div>
            <span class="duration-label">0:00</span>
            <audio src="{{ Storage::url($file->path) }}"></audio>
        </div>
        <button type="button" class="btn_remove_voice" onclick="formDeleteFile(this, {{ $file->id }});">
            <i class="fas fa-trash text-danger"></i>
        </button>
    </div>
</div>
