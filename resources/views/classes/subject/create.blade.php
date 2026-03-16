<div class="modal fade bd-example-modal-xl" id="create-subject-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h6 class="modal-title m-0" id="myExtraLargeLargeModalLabel">{{ __('action.subject.add')}}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="create-subject-form" action="{{ route('classes.subject.store') }}" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="name" class="form-label">{{__('general.name')}}</label>
                                <input type="text" name="name" class="form-control" id="name" value="" placeholder="Enter Name">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="description" class="form-label">{{__('general.description')}}</label>
                                <textarea name="description" class="form-control" id="description" cols="30" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer gap-1">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('action.cancel')}}</button>
                    <button type="button" onclick="submitForm(this);" class="btn btn-primary">{{ __('action.save')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
