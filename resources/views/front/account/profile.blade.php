@extends('front.layout.app')
@section('content')
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="#">My Account</a></li>
                    <li class="breadcrumb-item">Settings</li>
                </ol>
            </div>
        </div>
    </section>

    <section class=" section-11 ">
        <div class="container  mt-5">
            <div class="row">
                <div class="col-md-3">
                    @include('front.account.comman.sidebar')
                </div>
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="h5 mb-0 pt-2 pb-2">Personal Information</h2>
                        </div>
                        <form action="" name="profileForm" id="profileForm">
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="mb-3">
                                        <label for="name">Name</label>
                                        <input value="{{ $user->name }}" type="text" name="name" id="name"
                                            placeholder="Enter Your Name" class="form-control">
                                        <p></p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email">Email</label>
                                        <input value="{{ $user->email }}" type="text" name="email" id="email"
                                            placeholder="Enter Your Email" class="form-control">
                                        <p></p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="phone">Phone</label>
                                        <input value="{{ $user->phone }}" type="text" name="phone" id="phone"
                                            placeholder="Enter Your Phone" class="form-control">
                                        <p></p>
                                    </div>
                                    <div class="d-flex">
                                        <button type="submit" class="btn btn-dark">Update</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('customJs')
    <script>
        $('#profileForm').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('account.updateProfile') }}",
                type: "post",
                data: $(this).serializeArray(),
                dataType: "json",
                success: function(response) {
                    // console.log(response);

                    if (response.status == true) {
                        $('#name').removeClass('is-invalid').siblings('p').removeClass(
                            'invalid-feedback').html("")

                        $('#email').removeClass('is-invalid').siblings('p').removeClass(
                            'invalid-feedback').html("")
                            
                        $('#phone').removeClass('is-invalid').siblings('p').removeClass(
                            'invalid-feedback').html("")

                        $('#wishlist_modal .modal-body').html(response.message)
                        $('#wishlist_modal').modal('show')
                    } else {
                        let errors = response.errors

                        if (errors['name']) {
                            $('#name').addClass('is-invalid').siblings('p').addClass(
                                'invalid-feedback').html(errors['name'])
                        } else {
                            $('#name').removeClass('is-invalid').siblings('p').removeClass(
                                'invalid-feedback').html("")
                        }
                        if (errors['email']) {
                            $('#email').addClass('is-invalid').siblings('p').addClass(
                                'invalid-feedback').html(errors['email'])
                        } else {
                            $('#email').removeClass('is-invalid').siblings('p').removeClass(
                                'invalid-feedback').html("")
                        }
                        if (errors['phone']) {
                            $('#phone').addClass('is-invalid').siblings('p').addClass(
                                'invalid-feedback').html(errors['phone'])
                        } else {
                            $('#phone').removeClass('is-invalid').siblings('p').removeClass(
                                'invalid-feedback').html("")
                        }
                    }
                }
            })
        })
    </script>
@endsection
