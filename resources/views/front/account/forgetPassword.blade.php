@extends('front.layout.app')
@section('content')
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="/">Home</a></li>
                    <li class="breadcrumb-item">Forget Password</li>
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
                    <h4 class="modal-title">Forget Password</h4>
                    <div class="form-group">
                        <input  type="text" class="form-control" id="email" name="email" placeholder="Email">
                    <p class="invalid-feedback"></p>
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
<script>
    $('#resetForm').submit(function(e) {
        e.preventDefault();
        $('#btn').attr('disabled',true)
        $.ajax({
            url: "{{route('front.processForgetPassword')}}",
            type: 'post',
            data: $(this).serializeArray(),
            dataType: "json",
            success: function(response) {
                $('#btn').attr('disabled',false)
                console.log(response)
                if (response.status == true) {
                    window.location.href= "{{route('account.login')}}"
                } else {
                    let errors = response.errors
                    if (errors['email']) {
                        $('#email').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['email'])
                    } else {
                        $('#email').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("")
                    }
                }
            },
            error: function(JQXHR, execption) {
                console.log('Somothing went Wrong')
            }
        })
    })
</script>
@endsection
