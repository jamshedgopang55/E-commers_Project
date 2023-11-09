@extends('front.layout.app')
@section('content')
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="{{route('front.home')}}">Home</a></li>
                    <li class="breadcrumb-item">Reset Password</li>
                </ol>
            </div>
        </div>
    </section>

    <section class=" section-10">
        <div class="container">
            @if (Session::has('success'))
                <div class="alert alert-success">
                    {{ Session::get('success') }}
                </div>
            @endif
            @if (Session::has('error'))
            <div class="alert alert-danger">
                {{ Session::get('error') }}
            </div>
        @endif
            <div class="login-form">
                <form id="resetForm" method="POST" >
                    @csrf
                    <input type="text" name="token" hidden value="{{$token}}">
                    <h4 class="modal-title">Reset Password</h4>
                    <div class="form-group">
                        <input type="password" class="form-control" placeholder="Password" id="password" name="password">
                        <p></p>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" placeholder="Confirm Password"
                            id="password_confirmation" name="password_confirmation">
                        <p></p>
                    </div>

                    <input type="submit" id="btn" class="btn btn-dark btn-block btn-lg" value="Submit">
                </form>
                <div class="text-center small">Don't have an account? <a href="{{ route('account.register') }}">Sign up</a>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('customJs')
<SCript>
    $('#resetForm').submit(function(e) {
        e.preventDefault();
        $('#btn').attr('disabled',true)
        $.ajax({
            url: "{{ route('front.processResetPassword') }}",
            type: 'post',
            data: $(this).serializeArray(),
            dataType: "json",
            success: function(response) {
                $('#btn').attr('disabled',false)
                if (response['status'] === true) {
                    window.location.href= "{{route('account.login')}}"
                } else {
                    let errors = response.errors
                    if (errors['password']) {
                        $('#password').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['password'])
                    } else {
                        $('#password').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("")
                    }
                    if (errors['password_confirmation']) {
                        $('#password_confirmation').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['password_confirmation'])
                    } else {
                        $('#password_confirmation').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("")
                    }
                }
            },
            error : function () {
                $('#wishlist_modal .modal-body').html(" <div class='alert alert-danger'>Please Check Your Internet and Try  Again</div>")
                $('#wishlist_modal').modal('show')
                $('#btn').attr('disabled', false)
            }
        })
    })
</script>
@endsection
