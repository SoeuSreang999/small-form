<div class="position-relative">
    <img class="option-thumb-xxl rounded" src="{{ asset($image->thumbnail_image) }}" width="200" class="cursor-pointer" alt="" srcset="">
    <span class="position-absolute top-0 start-100 translate-middle rounded-circle bg-white cursor-pointer btn-item-remove" 
        onclick="optionImageRemove(this, {{ $option->id }});">
        <small class="thumb-xs">
            <i class="fas fa-trash fw-bold fs-10 text-danger"></i>
        </small>
    </span>
</div>