@php
    $type = strtolower($file->type ?? '');
    $videoTypes = ['mp4', 'webm', 'mov', 'avi', 'mkv', 'm4v'];
@endphp

@if ($file->isAudio())
    @include('forms.files.view.audio')
@elseif ($file->isImage())
    @include('forms.files.view.image')
@elseif (in_array($type, $videoTypes))
    @include('forms.files.view.video')
@elseif ($file->isDocument())
    @include('forms.files.view.document')
@endif