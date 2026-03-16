<script>

    function UserEdit(e, id){
        const url = "{{ route('setups.users.edit', ':id') }}".replace(':id', id);
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
                $("#modal-element #edit-user-modal").modal('show');
            },
            error: function(xhr) {
                showLoader(false);
            }
        });
    }

    $(document).on('submit', '#edit-user-form', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
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
            $("#modal-element #edit-user-modal").modal('hide');
            $("#users-table").DataTable().ajax.reload();
            Toast.fire({
                icon: "success",
                title: "Signed in successfully"
            });
        },
        error: function(xhr) {
            showLoader(false);
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                $('form.input.required').remove();
                $.each(errors, function(key, value) {
                    let input = $('[name="'+key+'"]');
                    input.addClass('required');
                });
            }
        }
    });
    });

    function UserDelete(e, id) {
        const url = "{{ route('setups.users.destroy', ':id') }}".replace(':id', id);
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
            }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: "DELETE",
                    success: function(response) {
                        $("#users-table").DataTable().ajax.reload();
                        notyf.open({
                            type: 'success',
                            message: `
                            <h5>Success!</h5>
                            <p>Data has been saved successfully.</p>
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
        var table = $('#users-table').DataTable();
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
