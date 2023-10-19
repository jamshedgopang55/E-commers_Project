@extends('admin.layout.app')
@section('content')
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create Edit</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('coupons.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            <form method="GET" id="discountForm">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">code</label>
                                    <input type="text" name="code" value="{{ $coupon->code }}" id="code"
                                        class="form-control" placeholder="Coupon Code">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="slug">name</label>
                                    <input type="text" name="name" id="name" value="{{ $coupon->name }}"
                                        class="form-control" placeholder="Coupon Code Name">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="slug">Max Uses</label>
                                    <input type="number" name="max_uses" id="max_uses" value="{{ $coupon->max_uses }}"
                                        class="form-control" placeholder="Max Uses">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="slug">Max Uses User</label>
                                    <input type="number" name="max_uses_user" value="{{ $coupon->max_uses_user }}"
                                        id="max_uses_user" class="form-control" placeholder="Max Uses User">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type">type</label>
                                    <select name="type" id="type" class="form-control" id="">
                                        <option {{ ($coupon->type == 'percent') ? 'selected' : '' }} value="percent">present</option>
                                        <option  {{ ($coupon->type == 'fixed') ? 'selected' : '' }}  value="fixed">Fixed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="discount_amount">Descount Amount</label>
                                    <input type="number" name="discount_amount" value="{{ $coupon->discount_amount }}"
                                        id="discount_amount" class="form-control" placeholder="Descount Amount">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="min_amount">Min Amount</label>
                                    <input type="number" name="min_amount" value="{{ $coupon->min_amount }}"
                                        id="min_amount" class="form-control" placeholder="Min Amount">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">status</label>
                                    <select name="status" id="status" class="form-control" id="">
                                        <option {{ $coupon->status == 1 ? 'selected' : '' }} value="1">Active
                                        </option>
                                        <option {{ $coupon->status == 0 ? 'selected' : '' }} value="0">Blocked
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_at">Start At</label>
                                    <input autocomplete="off" type="text" value="{{ $coupon->start_at }}" name="start_at" id="start_at"
                                        class="form-control" placeholder="Start At">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expires_at">Expires At</label>
                                    <input autocomplete="off" type="text" name="expires_at" value="{{ $coupon->expires_at }}"
                                        id="expires_at" class="form-control" placeholder="Expires At">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="slug">Description</label>
                                    <textarea name="description" id="description" cols="30" rows="5" class="form-control">{{       $coupon->description}}</textarea>
                                    <p></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pb-5 pt-3">
                    <button id="btn" type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('coupons.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>

            </form>
        </div>
        <!-- /.card -->
    </section>
@endsection
@section('customJs')
    <script>
        $(document).ready(function() {
            $('#start_at').datetimepicker({
                // options here
                format: 'Y-m-d H:i:s',
            });
            $('#expires_at').datetimepicker({
                // options here
                format: 'Y-m-d H:i:s',
            });
        });
        $(document).ready(function() {
            $('#discountForm').submit('click', function(e) {
                $('#btn').attr('disabled', true)
                const data = $(this).serializeArray()
                e.preventDefault();
                $.ajax({
                    url: "{{ route('coupons.update',$coupon->id) }}",
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        $('#btn').attr('disabled', false)
                        if (response['status'] == true) {
                            window.location.href = " {{ route('coupons.index') }}"
                        } else {
                            let errors = response.errors
                            if (errors['code']) {
                                $('#code').addClass('is-invalid').siblings('p').addClass(
                                    'invalid-feedback').html(errors['code'])
                            } else {
                                $('#code').removeClass('is-invalid').siblings('p').removeClass(
                                    'invalid-feedback').html("")
                            }

                            if (errors['type']) {
                                $('#type').addClass('is-invalid').siblings('p').addClass(
                                    'invalid-feedback').html(errors['type'])
                            } else {
                                $('#type').removeClass('is-invalid').siblings('p').removeClass(
                                    'invalid-feedback').html("")
                            }

                            if (errors['status']) {
                                $('#status').addClass('is-invalid').siblings('p').addClass(
                                    'invalid-feedback').html(errors['status'])
                            } else {
                                $('#type').removeClass('is-invalid').siblings('p').removeClass(
                                    'invalid-feedback').html("")
                            }

                            if (errors['discount_amount']) {
                                $('#discount_amount').addClass('is-invalid').siblings('p')
                                    .addClass(
                                        'invalid-feedback').html(errors['discount_amount'])
                            } else {
                                $('#discount_amount').removeClass('is-invalid').siblings('p')
                                    .removeClass(
                                        'invalid-feedback').html("")
                            }

                            if (errors['start_at']) {
                                $('#start_at').addClass('is-invalid').siblings('p').addClass(
                                    'invalid-feedback').html(errors['start_at'])
                            } else {
                                $('#start_at').removeClass('is-invalid').siblings('p')
                                    .removeClass(
                                        'invalid-feedback').html("")
                            }

                            if (errors['expires_at']) {
                                $('#expires_at').addClass('is-invalid').siblings('p').addClass(
                                    'invalid-feedback').html(errors['expires_at'])
                            } else {
                                $('#expires_at').removeClass('is-invalid').siblings('p')
                                    .removeClass(
                                        'invalid-feedback').html("")
                            }

                        }

                    }
                })
            })
        })
    </script>
@endsection
