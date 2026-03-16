<script>
    function SubjectCreate(el){
        const url = "{{ route('classes.subject.create') }}";
        $.ajax({
            url: url,
            type: "GET",
            beforeSend: function() {
                showLoader(true);
            },
            dataType: "HTML",
            success: function(response) {
                showLoader(false);
                $("#modal-element").html(response);
                $("#modal-element #create-subject-modal").modal('show');
            },
            error: function(xhr) {
                showLoader(false);
            }
        });
    }

    function submitForm(button) {
        var form        = $(button).closest('form');
        var formData    = form.serialize();
        var url         = form.attr('action');
        var method      = form.attr('method');

        $.ajax({
            url: url,
            type: method,
            data: formData,
            beforeSend: function() {
                showLoader(true);
            },
            success: function(response) {
                let title   = response.title;
                let message = response.message;
                showLoader(false);
                $("#subject-table").DataTable().ajax.reload();
                $("#modal-element #create-subject-modal").modal('hide');
                notyf.open({
                    type: 'success',
                    message: `
                    <h5>${title}</h5>
                    <p>${message}</p>
                    `
                });
            },
            error: function(xhr) {
                showLoader(false);
            }
        });
    }

    function SubjectEdit(el, id){
        const url = "{{ route('classes.subject.edit', ':id') }}".replace(':id', id);
        $.ajax({
            url: url,
            type: "GET",
            beforeSend: function() {
                showLoader(true);
            },
            dataType: "HTML",
            success: function(response) {
                showLoader(false);
                $("#modal-element").html(response);
                $("#modal-element #edit-subject-modal").modal('show');
            },
            error: function(xhr) {
                showLoader(false);
            }
        });
    }

    $(document).on('submit', '#edit-subject-form', function(e) {
        e.preventDefault();
        var form        = $(this);
        var formData    = new FormData(this);
        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                showLoader(true);
            },
            success: function(response) {
                showLoader(false);
                let title   = response.title;
                let message = response.message;
                $("#modal-element #edit-subject-modal").modal('hide');
                $("#subject-table").DataTable().ajax.reload();
                notyf.open({
                    type: 'success',
                    message: `
                    <h5>${title}</h5>
                    <p>${message}</p>
                    `
                });
            },
            error: function(xhr) {
                showLoader(false);
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    form.find('.required').removeClass('required');
                    form.addClass('was-validated');
                    $.each(errors, function(key, value) {
                        let input = $('[name="'+key+'"]');
                        input.attr('required', true);
                    });
                }
            }
        });
    });

    function SubjectDelete(el, id) {
        const url = "{{ route('classes.subject.destroy', ':id') }}".replace(':id', id);
        Swal.fire({
            title: "{{ __('messages.confirm_delete') }}",
            text: {!! json_encode(__('messages.confirm_text')) !!},
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            cancelButtonText: "{{ __('messages.confirm_no') }}",
            confirmButtonText: "{{ __('messages.confirm_yes') }}",
            }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: "DELETE",
                    success: function(response) {
                        $("#subject-table").DataTable().ajax.reload();
                        notyf.open({
                            type: 'success',
                            message: `
                            <h5>{{ __('messages.success') }}</h5>
                            <p>{{ __('messages.success_delete')}}</p>
                            `
                        });
                    },
                    error: function(xhr) {
                    }
                });
            }
        });
    }

    $(document).ready(function() {
        var table = $('#subject-table').DataTable();
        var searchTimeout;
        $(document).on('input', 'input.search-input', function() {
            clearTimeout(searchTimeout);
            var searchValue = this.value;
            searchTimeout = setTimeout(function() {
                table.search(searchValue).draw();
            }, 600);
        });
    });
</script>
