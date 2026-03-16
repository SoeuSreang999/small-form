<div class="d-flex justify-content-center gap-2">
    <a class="btn btn-light" href="javascript:void(0);" onclick="UserEdit(this,{{ $user->id }});">
        <i class="fas fa-edit text-primary"></i>
    </a>
    <a class="btn btn-light" href="javascript:void(0);" onclick="UserDelete(this,{{ $user->id }});">
        <i class="fas fa-trash-alt text-danger"></i>
    </a>
</div>
