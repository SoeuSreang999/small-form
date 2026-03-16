<script>

    function addClasses(e, id){
        const url = "{{ route('classes.class.create') }}";
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
                $("#modal-element #create-classes-modal").modal('show');
            },
            error: function(xhr) {
                showLoader(false);
            }
        });
    }

    $(document).on('submit', '#create-classes-form', function(e) {
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
                $("#modal-element #create-classes-modal").modal('hide');
                $("#classes-table").DataTable().ajax.reload();
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
                handleValidationErrors(form, xhr);
            }
        });
    });

    function classEdit(e, id){
        const url = "{{ route('classes.class.edit', ':id') }}".replace(':id', id);
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
                $("#modal-element #edit-classes-modal").modal('show');
            },
            error: function(xhr) {
                showLoader(false);
                handleErrorMessage(xhr);
            }
        });
    }

    $(document).on('submit', '#edit-classes-form', function(e) {
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
                $("#modal-element #edit-classes-modal").modal('hide');
                $("#classes-table").DataTable().ajax.reload();
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
                handleValidation(form, xhr);
            }
        });
    });

    function classDelete(e, id) {
        const url = "{{ route('classes.class.destroy', ':id') }}".replace(':id', id);
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
                    dataType: "JSON",
                    success: function(response) {
                        let title       = response.title;
                        let message     = response.message;
                        $("#classes-table").DataTable().ajax.reload();
                        notyf.open({
                            type: 'success',
                            message: `
                            <h5>${title}</h5>
                            <p>${message}</p>
                            `
                        });
                    },
                    error: function(xhr) {
                        handleErrorMessage(xhr);
                    }
                });
            }
        });
    }

    $(document).ready(function() {
        var table = $('#classes-table').DataTable();
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
