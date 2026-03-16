<div class="modal fade bd-example-modal-xl" id="edit-classes-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="edit-classes-form" action="{{ route('classes.class.update', $class->id) }}" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="name" class="form-label">{{__('general.name')}}</label>
                                <input type="text" name="name" class="form-control" id="name" value="{{ $class->name }}" placeholder="Enter Name">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-2">
                                <label class="form-label">@lang('general.subject')</label>
                                <select class="form-select2 w-100" name="subjects[]" multiple>
                                    @forelse ($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ in_array($subject->id, $class_subjects ?? []) ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @empty
                                        <option disabled>{{ __('general.no_data') }}</option>
                                    @endforelse
                                </select>         
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="description" class="form-label">{{__('general.description')}}</label>
                                <textarea name="description" class="form-control" id="description" cols="30" rows="3">{{ $class->description }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer gap-1">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('action.cancel')}}</button>
                    <button type="submit" class="btn btn-primary">{{ __('action.save')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('.form-select2').each(function() {
        var $this           = $(this);
        var dynamicWidth    = getWidthFromClasses($this);
        var $parentModal    = $this.closest('.modal');
        $this.select2({
            minimumResultsForSearch: -1,
            dropdownParent: $parentModal.length ? $parentModal : $(document.body),
            width: dynamicWidth
        });
    });
</script>
