<div class="modal fade" id="create-classes-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
        <div class="modal-content">
            <form class="needs-validation" id="create-classes-form" action="{{ route('classes.class.store') }}" method="POST" novalidate>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-2">
                                <label for="name" class="form-label">@lang('general.classes.name')</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="@lang('general.enter') @lang('general.classes.name')" required="required">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-2">
                                <label class="form-label">@lang('general.subject')</label>
                                <select class="form-select2" name="subjects[]" multiple>
                                    @forelse ($subjects as $subject)
                                        <option value="{{$subject->id??null}}">{{$subject->name??null}}</option>
                                    @empty
                                        
                                    @endforelse
                                </select>         
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-2">
                                <label for="description" class="form-label">@lang('general.description')</label>
                                <textarea name="description" id="description" class="form-control" placeholder="@lang('general.enter') @lang('general.description')"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer gap-1">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('action.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('action.save') }}</button>
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