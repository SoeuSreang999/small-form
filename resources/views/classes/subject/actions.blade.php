<div class="dropdown d-inline-block">
    <a class="dropdown-toggle arrow-none d-flex align-items-center pt-1" id="dLabel11" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
        <i class="fas fa-bars fs-14 me-2"></i>
        <i class="fas fa-caret-down fs-12"></i>
        {{-- <i class="las la-bars fs-20 me-1"></i>
        <i class="las la-angle-down fs-12"></i> --}}
    </a>
    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dLabel11">
        <a class="dropdown-item text-success" href="javascript:void(0);" onclick="SubjectEdit(this,{{ $subject->id }});">
            <i class="fas fa-pencil-alt fs-12 me-1"></i>
            @lang('action.edit')
        </a>
        <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="SubjectDelete(this,{{ $subject->id }});">
            <i class="fas fa-trash-alt fs-12 me-1"></i>
            @lang('action.delete')
        </a>
    </div>
</div>
