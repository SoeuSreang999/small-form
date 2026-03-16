
<div class="col-md-12 option-file {{($image !=null)?'mb-2':'mb-0'}} d-flex justify-content-between align-items-center gap-3">
    <div class="position-relative">
        <img class="rounded" src="{{ asset($image->thumbnail_image) }}" width="200" class="cursor-pointer" alt="" srcset="">
        <span class="position-absolute top-0 start-100 translate-middle rounded-circle bg-white cursor-pointer btn-item-remove" 
            onclick="questionRemoveImage(this, {{ $question->id }});">
            <small class="thumb-xs">
                <i class="fas fa-trash fw-bold fs-10 text-danger"></i>
            </small>
        </span>
    </div>
</div>