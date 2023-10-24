@extends('front.layout.app')
@section('content')
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('front.home') }}">Home</a></li>
                    <li class="breadcrumb-item">{{ $page->name }}</li>
                </ol>
            </div>
        </div>
    </section>
    @if ($page->slug == 'contect-us')
        <section class=" section-10">
            <div class="container">
                <div class="section-title mt-5 ">
                    <h2>{{ $page->name }}</h2>
                </div>
            </div>
        </section>
        <section>
            <div class="container">
                <div class="row">
                    <div class="col-md-6 mt-3 pe-lg-5">
                        {!! $page->content !!}
                    </div>

                    <div class="col-md-6">
                        <form class="shake" role="form" method="post" id="contactForm" name="contact-form">
                            <div class="mb-3">
                                <label class="mb-2" for="name">Name</label>
                                <input class="form-control" id="name" type="text" name="name"
                                    data-error="Please enter your name">
                                <p></p>
                            </div>

                            <div class="mb-3">
                                <label class="mb-2" for="email">Email</label>
                                <input class="form-control" id="email" name="email"
                                    data-error="Please enter your Email">
                                <p></p>
                            </div>

                            <div class="mb-3">
                                <label class="mb-2">Subject</label>
                                <input class="form-control" id="msg_subject" type="text" name="msg_subject"
                                    data-error="Please enter your message subject">
                                <p></p>
                            </div>

                            <div class="mb-3">
                                <label for="message" class="mb-2">Message</label>
                                <textarea class="form-control" rows="3" id="message" name="message" data-error="Write your message"></textarea>
                                <p></p>
                            </div>

                            <div class="form-submit">
                                <button id="btn" class="btn btn-dark" type="submit" id="form-submit"><i
                                        class="material-icons mdi mdi-message-outline"></i> Send Message</button>
                                <div id="msgSubmit" class="h3 text-center hidden"></div>
                                <div class="clearfix"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    @else
        <section class=" section-10">
            <div class="container">
                <h1 class="my-3">{{ $page->name }}</h1>
                {!! $page->content !!}

            </div>
        </section>
    @endif
@endsection
@section('customJs')
    <script>
        $('#contactForm').submit(function(e) {
                    $('#btn').attr('disabled', true)
                    e.preventDefault();
                    $('#name').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("")
                    $('#email').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("")
                    $('#msg_subject').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("")
                    $('#message').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("")
                    $('#message').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("")
                    $.ajax({
                            url: '{{ route('front.sendContectEmail') }}',
                            data: $(this).serializeArray(),
                            type: 'POST',
                            dataType: 'json',
                            beforeSend: function() {
                                $('#loader').removeClass('d-none');
                            },
                            complete: function() {
                                // Hide the loader when the request is complete
                                $('#loader').addClass('d-none');
                            },
                                success: function(response) {
                                    $('#btn').attr('disabled', false)
                                    if (response.status == true) {

                                        $('#name').val('')
                                        $('#email').val('')
                                        $('#msg_subject').val('')
                                        $('#message').val('')


                                        $('#wishlist_modal .modal-body').html(response.message)
                                        $('#wishlist_modal').modal('show')

                                    } else {
                                        let errors = response.errors
                                        if (errors['name']) {
                                            $('#name').addClass('is-invalid').siblings('p').addClass(
                                                    'invalid-feedback')
                                                .html(errors['name'])
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
                                        if (errors['msg_subject']) {
                                            $('#msg_subject').addClass('is-invalid').siblings('p').addClass(
                                                'invalid-feedback').html(errors['msg_subject'])
                                        } else {
                                            $('#msg_subject').removeClass('is-invalid').siblings('p')
                                                .removeClass(
                                                    'invalid-feedback').html("")
                                        }

                                        if (errors['message']) {
                                            $('#message').addClass('is-invalid').siblings('p').addClass(
                                                'invalid-feedback').html(errors['message'])
                                        } else {
                                            $('#message').removeClass('is-invalid').siblings('p').removeClass(
                                                'invalid-feedback').html("")
                                        }

                                    }
                                }
                            })
                    })
    </script>
@endsection
