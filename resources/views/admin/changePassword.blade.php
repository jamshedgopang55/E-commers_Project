@extends('admin.layout.app')
@section('content')
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Change Password</h1>
                </div>

            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            <form method="POST" id="changePasswordForm">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="name">Old Password*</label>
                                    <input type="password" name="old_password" id="old_password" class="form-control"
                                        placeholder="old Password">
                                    <p></p>

                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="name">New Password*</label>
                                    <input type="password" name="new_password" id="new_password" class="form-control"
                                        placeholder="New Password">
                                    <p></p>

                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="name">Confirm Password*</label>
                                    <input type="password" name="confirm_password" id="confirm_password" class="form-control"
                                        placeholder="old Password">
                                    <p></p>

                                </div>
                            </div>



                        </div>
                    </div>
                </div>
                <div class="pb-5 pt-3">
                    <button id="btn" type="submit" class="btn btn-primary">Change</button>
                    {{-- <a href="{{ route('') }}" class="btn btn-outline-dark ml-3">Cancel</a> --}}
                </div>

            </form>
        </div>
        <!-- /.card -->
    </section>
@endsection
@section('customJs')
    <script>
        $('#changePasswordForm').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: '{{ route('admin.updatePassword') }}',
                data: $(this).serializeArray(),
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.status == true) {
                        $('#new_password').removeClass('is-invalid').siblings('p').removeClass(
                                'invalid-feedback').html("")

                                $('#confirm_password').removeClass('is-invalid').siblings('p').removeClass(
                                'invalid-feedback').html("")

                                if(response.success ==  true){
                                    $('#old_password').removeClass('is-invalid').siblings('p').removeClass(
                                'invalid-feedback').html("")
                                $('#old_password').val('')
                                $('#new_password').val('')
                                $('#confirm_password').val('')

                                }else{
                                    $('#old_password').addClass('is-invalid')
                                }
                        $('#wishlist_modal .modal-body').html(response.message)
                         $('#wishlist_modal').modal('show')
                    } else {
                        let errors = response.errors
                        if (errors['old_password']) {
                            $('#old_password').addClass('is-invalid').siblings('p').addClass('invalid-feedback')
                                .html(errors['old_password'])
                        } else {
                            $('#old_password').removeClass('is-invalid').siblings('p').removeClass(
                                'invalid-feedback').html("")
                        }
                        if (errors['new_password']) {
                            $('#new_password').addClass('is-invalid').siblings('p').addClass(
                                'invalid-feedback').html(errors['new_password'])
                        } else {
                            $('#new_password').removeClass('is-invalid').siblings('p').removeClass(
                                'invalid-feedback').html("")
                        }
                        if (errors['confirm_password']) {
                            $('#confirm_password').addClass('is-invalid').siblings('p').addClass(
                                'invalid-feedback').html(errors['confirm_password'])
                        } else {
                            $('#confirm_password').removeClass('is-invalid').siblings('p').removeClass(
                                'invalid-feedback').html("")
                        }

                    }
                }
            })
        })
    </script>
@endsection
