<div class="modal fade bd-example-modal-xl" id="edit-user-modal" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h6 class="modal-title m-0" id="myExtraLargeLargeModalLabel">Edit User</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit-user-form" action="{{ route('setups.users.update', $user->id) }}" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control" id="first_name" value="{{ $user->first_name }}" placeholder="Enter First Name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" id="last_name" value="{{ $user->last_name }}" placeholder="Enter Last Name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" autocomplete="username" name="email" class="form-control" id="email" value="{{ $user->email }}" placeholder="Enter Email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_of_birth" class="form-label">Date of Birth</label>
                                <input type="text" name="date_of_birth" class="form-control" id="date_of_birth" value="{{ date('d-m-Y', strtotime($user->date_of_birth)) }}" placeholder="Enter Date of Birth">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" name="phone" class="form-control" id="phone" value="{{ $user->phone??null }}" placeholder="Enter Phone Number">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select name="gender" id="gender" class="form-select form-select2">
                                    <option {{ ($user->gender == 'male')?'selected':'' }} value="male">Male</option>
                                    <option {{ ($user->gender == 'female')?'selected':'' }} value="female">Female</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="role_id" class="form-label">Role</label>
                                <select name="role_id" id="role_id" class="form-select form-select2">
                                    <option {{ ($user->gender == 'user')?'selected':'' }} value="user">User</option>
                                    <option {{ ($user->gender == 'male')?'student':'' }} value="student">Student</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" autocomplete="new-password" class="form-control" id="password" placeholder="Enter Password">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="retype-password" class="form-label">Retype Password</label>
                                <input type="password" name="retype_password" autocomplete="new-password" class="form-control" id="retype-password" placeholder="Retype Password">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer gap-1">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>

</style>
<script>
    $(document).ready(function() {
        var selectedDate    = "{{ date('d-m-Y', strtotime($user->date_of_birth)) }}";
        var maxYear         = new Date().getFullYear() - 5;
        $('#date_of_birth').daterangepicker({
            opens: 'right',
            singleDatePicker: true,
            autoApply: true,
            showDropdowns: true,
            minDate: '01-01-1950',
            maxDate: '31-12-' + maxYear,
            startDate: selectedDate,
            locale: {
                format: 'DD-MM-YYYY'
            },
        });

        $('.form-select2').select2({
            minimumResultsForSearch: -1,
            dropdownParent: $('#edit-user-modal'),
            width: '100%'
        });
    });
</script>
