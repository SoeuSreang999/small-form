@php
    $setting            = $question->QuestionSetting;
    $maxFile            = max(1, (int) ($setting?->max_file ?? 1));
    $isMultipleFile     = ($maxFile > 1) ? true : false;
    $maxSize            = (int) ($setting?->max_size ?? 0);

    $allowedTypeNames   = [];
    $allowedMimeTypes   = [];
    $allowedExtensions  = [];
    if (($setting?->specific_file ?? 0) == 1) {
        $allowedIds = $question->questionSpecificFileSettings
            ->pluck('question_specific_file_id')
            ->toArray();

        $allowedTypes       = collect($fileTypes ?? [])->whereIn('id', $allowedIds);
        $allowedTypeNames   = $allowedTypes->pluck('name')->toArray();

        $allowedMimeTypes   = $allowedTypes
            ->pluck('mime_type')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $allowedExtensions = $allowedTypes
            ->pluck('extension')
            ->filter()
            ->flatMap(function ($extensions) {
                return preg_split('/\s*,\s*/', (string) $extensions, -1, PREG_SPLIT_NO_EMPTY);
            })
            ->map(function ($ext) {
                return strtolower(ltrim(trim((string) $ext), '.'));
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
@endphp

<div class="col-12">
    <input
        type="file"
        class="form-control dropify"
        data-height="150"
        data-max-files="{{ $maxFile }}"
        @if ($maxSize > 0)
            data-max-file-size="{{ $maxSize }}M"
        @endif
        @if (!empty($allowedMimeTypes))
            accept="{{ implode(',', $allowedMimeTypes) }}"
        @endif
        @if (!empty($allowedExtensions))
            data-allowed-file-extensions="{{ implode(' ', $allowedExtensions) }}"
        @endif
        @if ($isMultipleFile)
            multiple
        @endif
        name="files[{{ $question->id }}][]"
    >
    <small class="form-text text-muted fs-6">
        @if ($maxFile > 1)
            Max files: {{ $maxFile }}
        @endif
        @if ($maxSize > 0)
            {{ $maxFile > 1 ? ' • ' : '' }}Max size: {{ $maxSize }}MB per file
        @endif
        @if (!empty($allowedTypeNames))
            {{ ($maxFile > 1 || $maxSize > 0) ? ' • ' : '' }}Allowed: {{ implode(', ', $allowedTypeNames) }}
        @endif
    </small>
</div>
