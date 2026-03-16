@php
    $secId      = $section->id;
    $secInd     = ($secInd == 0)?($secInd + 1):0;
    $secActive  = ($secInd == 1)?'active':'';
    $secFiles   = $section->files();
@endphp

<form action="" method="POST">
    <div class="card item section-item"
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
                    <h3 class="mb-2 section-title">{{ $section->name_en ?? '' }}</h3>
                    <p class="section-desc text-muted fs-5">{!! $section->desc_en ?? '' !!}</p>
                    @if ($secInd === 1 AND $formDuration > 0)
                        <div class="col-auto mt-2 ms-auto">
                            <div class="mb-2 px-3 d-flex gap-3 justify-content-end">
                                <div class="tracking-date fs-3">
                                    <i class="mdi mdi-clock-outline fs-3"></i>
                                </div>
                                <div class="tracking-date fs-3">
                                    <p class="p-0 m-0 hours_duration">00</p>
                                    <span class="d-block fs-12 text-muted text-center">HH</span>
                                </div>
                                <div class="tracking-date fs-3">
                                    <p class="p-0 m-0 minutes_duration">00</p>
                                    <span class="d-block fs-12 text-muted text-center">MM</span>
                                </div>
                                <div class="tracking-date fs-3">
                                    <p class="p-0 m-0 seconds_duration">00</p>
                                    <span class="d-block fs-12 text-muted text-center">SS</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</form>
