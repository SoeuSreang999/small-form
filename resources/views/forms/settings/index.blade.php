@php
    $duration               = $form->duration ?? 0;
    $publish_result_date    = formDate($form->publish_result_date);
    $isImmediateResult      = $form->isImmediateResult();
@endphp

<form action="{{ route('forms.settings.update', ['form' => $form->uuid]) }}" method="POST" id="settings_form">
    <div class="row" id="setting">
        <div class="col-12 mt-3">
            <div class="card border-1 rounded-2">
                <div class="card-header border-bottom">
                    <h4 class="card-title fs-5">{{ __('general.form_settings') }}</h4>
                </div>

                <div class="card-body p-0">
                    <div class="px-3 py-3 border-bottom">
                        <div class="row align-items-center g-3">
                            <div class="col-lg-4">
                                <label for="duration" class="form-label text-black-90 fw-bolder fs-5 mb-1">
                                    {{ __('general.duration') }} ({{ __('general.minutes') }})
                                </label>
                                <small class="text-muted fs-6 d-block">
                                    {{ __('general.duration_hint') ?? 'Set 0 for no time limit.' }}
                                </small>
                            </div>

                            <div class="col-lg-8">
                                <div class="input-group w-auto">
                                    <span class="input-group-text">
                                        <i class="mdi mdi-clock-outline"></i>
                                    </span>
                                    <input
                                        type="number"
                                        name="duration"
                                        class="form-control"
                                        width="150"
                                        id="duration"
                                        value="{{ $duration }}"
                                        min="0"
                                        placeholder="{{ __('general.enter_duration') ?? 'Enter duration' }}"
                                    >
                                    <span class="input-group-text">{{ __('general.minutes') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-3 py-3 border-bottom">
                        <div class="row align-items-center g-3">
                            <div class="col-lg-4">
                                <label class="form-label text-black-90 fw-bolder fs-5 mb-1 d-block" for="customSwitchSuccess{{ $form->id ?? 0 }}">
                                    {{ __('general.immediate_results') }}
                                </label>
                                <small class="text-muted fs-6 d-block">
                                    {{ __('general.immediate_results_hint') ?? 'Users can view results right after submitting.' }}
                                </small>
                            </div>

                            <div class="col-lg-8">
                                <div class="form-check form-switch mb-0">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="customSwitchSuccess{{ $form->id ?? 0 }}"
                                        name="immediate_results"
                                        value="1"
                                        {{ $isImmediateResult ? 'checked' : '' }}
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-3 py-3 border-bottom" id="publish_result_date_container">
                        <div class="row align-items-center g-3">
                            <div class="col-lg-4">
                                <label for="publish_result_date" class="form-label text-black-90 fw-bolder fs-5 mb-1">
                                    {{ __('general.publish_result_date') }}
                                </label>
                                <small class="text-muted fs-6 d-block">
                                    {{ __('general.publish_result_date_hint') ?? 'Results will be visible on this date.' }}
                                </small>
                            </div>

                            <div class="col-lg-8">
                                <div id="publish_date_box" style="{{ $isImmediateResult ? 'opacity: 0.5; pointer-events: none; cursor: not-allowed;' : '' }}">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="mdi mdi-calendar-month"></i>
                                        </span>
                                        <input
                                            type="text"
                                            name="publish_result_date"
                                            class="form-control"
                                            id="publish_result_date"
                                            value="{{ $publish_result_date }}"
                                            {{ $isImmediateResult ? 'disabled' : '' }}
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-3 py-3 border-bottom" id="publish_result_date_container">
                        <div class="row align-items-center g-3">
                            <div class="col-lg-4">
                                <label for="publish_result_date" class="form-label text-black-90 fw-bolder fs-5 mb-1">
                                    {{ __('general.specific_assign') }}
                                </label>
                                <small class="text-muted fs-6 d-block">
                                    {{ __('general.specific_assign_hint') ?? 'Assign specific users or groups.' }}
                                </small>
                            </div>

                            <div class="col-lg-8">
                                <div id="specific_assign">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="mdi mdi-account-multiple"></i>
                                        </span>
                                        <input
                                            type="text"
                                            name="specific_assign"
                                            class="form-control"
                                            id="specific_assign"
                                            value=""
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
    @include('forms.settings.script_setting')
@endpush
