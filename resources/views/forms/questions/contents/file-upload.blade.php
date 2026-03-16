@php
    $seeting            = $question->QuestionSetting;
    $isSpecific_file    = ($seeting?->specific_file === 1)?'checked':null;
    $specificFiles      = $question->questionSpecificFileSettings;
    $specificFilesArr   = $specificFiles->pluck('question_specific_file_id')->toArray();
@endphp

<div class="col-md-12 px-2" id="question-item{{ $question->id }}">
    <div class="border p-3 px-3 rounded">
        <div class="form-check form-switch mb-3">
            <input
                name="specific_file"
                class="form-check-input"
                type="checkbox"
                value="1"
                {{ $isSpecific_file }}
                id="flexSwitchCheck{{ $question->id }}"
            >
            <label class="fw-medium" for="flexSwitchCheck{{ $question->id }}">
                Allow only specific file types
            </label>
        </div>
        <div class="row col-6 file-type-options {{ ($isSpecific_file == 'checked')?'show': null  }}">
            @foreach($fileTypes as $fileType)
                <div class="col-md-6">
                    <div class="form-check">
                        <input
                            class="form-check-input check-input-file-type"
                            type="checkbox"
                            value="{{ $fileType->id }}"
                            id="fileType{{ $question->id }}{{ $fileType->id }}"
                            name="file_types[]"
                            {{ in_array($fileType->id, $specificFilesArr)?'checked':null }}
                        >
                        <label class="form-check-label" for="fileType{{ $question->id }}{{ $fileType->id }}">
                            {{ $fileType->name }}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="row mt-2">
            <div class="col-ms-12 col-md-4 col-lg-4">
                <label class="mb-1">Maximum number of files</label>
                <select class="form-control form-select2 setting" name="max_file">
                    <option value="1" {{ $seeting->max_file === 1 ? 'selected' : '' }}>1</option>
                    <option value="2" {{ $seeting->max_file === 2 ? 'selected' : '' }}>2</option>
                    <option value="3" {{ $seeting->max_file === 3 ? 'selected' : '' }}>3</option>
                    <option value="4" {{ $seeting->max_file === 4 ? 'selected' : '' }}>4</option>
                    <option value="5" {{ $seeting->max_file === 5 ? 'selected' : '' }}>5</option>
                </select>
            </div>
            <div class="col-ms-12 col-md-4 col-lg-4">
                <label class="mb-1">Maximum file size</label>
                <select class="form-control form-select2 setting" name="max_size">
                    <option value="1" {{ $seeting->max_size === 1 ? 'selected' : '' }}>1 MB</option>
                    <option value="2" {{ $seeting->max_size === 2 ? 'selected' : '' }}>2 MB</option>
                    <option value="5" {{ $seeting->max_size === 5 ? 'selected' : '' }}>5 MB</option>
                    <option value="10" {{ $seeting->max_size === 10 ? 'selected' : '' }}>10 MB</option>
                </select>
            </div>
        </div>
    </div>
</div>