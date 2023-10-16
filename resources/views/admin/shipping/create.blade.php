@extends('admin.layout.app')
@section('content')
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Shippping Manegement</h1>
                </div>
                <div class="col-sm-6 text-right">
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            @include('admin.message')
            <form method="GET" id="shippingForm">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <select name="country" id="country" class="form-control">
                                        <option value="">Select a Country</option>
                                        @if ($countries->isNotEmpty())
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                                            @endforeach
                                            <option value="rest_of_world">Rest Of the world</option>
                                        @endif
                                    </select>
                                    <p></p>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <input type="number" name="amount" id="amount" placeholder="Amount"
                                        class="form-control">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <button id="btn" type="submit" class="btn btn-primary">Create</button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                    @if ($shippingCharges->isNotEmpty())
                                        @foreach ($shippingCharges as $shippingCharge)
                                            <tr>
                                                <td>{{ $shippingCharge->id }}</td>
                                                <td>{{ $shippingCharge->country_id == 'rest_of_world' ? 'Rest of the World' : $shippingCharge->name }}
                                                </td>
                                                <td>${{ $shippingCharge->amount }}</td>
                                                <td>
                                                    <a href="{{ route('shipping.edit', $shippingCharge->id) }}"
                                                        class="btn btn-primary">Edit</a>
                                                    <a href="javascript:void(0)" onclick="deleteRecode({{$shippingCharge->id}})" class="btn btn-danger">Delete</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </section>
@endsection
@section('customJs')
    <script>
        $(document).ready(function() {
            $('#shippingForm').submit('click', function(e) {
                $('#btn').attr('disabled', true)
                const data = $(this).serializeArray()

                e.preventDefault();
                $.ajax({
                    url: "{{ route('shipping.store') }}",
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        $('#btn').attr('disabled', false)
                        if (response['status'] == true) {
                            window.location.href = " {{ route('shipping.create') }}"
                        } else {
                            let errors = response.errors

                            if (errors['country']) {
                                $('#country').addClass('is-invalid').siblings('p').addClass(
                                    'invalid-feedback').html(errors['country'])
                            } else {
                                $('#country').removeClass('is-invalid').siblings('p')
                                    .removeClass(
                                        'invalid-feedback').html("")
                            }

                            if (errors['amount']) {
                                $('#amount').addClass('is-invalid').siblings('p').addClass(
                                    'invalid-feedback').html(errors['amount'])
                            } else {
                                $('#amount').removeClass('is-invalid').siblings('p')
                                    .removeClass(
                                        'invalid-feedback').html("")
                            }


                        }

                    }
                })
            })
        })

        function deleteRecode(id) {
            if (confirm('Are You Sure You Went To delete?')) {
                let url = '{{ route('shipping.delete', 'id') }}';
                let newUrl = url.replace('id', id)
                    $.ajax({
                        url: newUrl,
                        type: "post",
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.status) {
                                window.location.href = "{{ route('shipping.create') }}"
                            }
                        }
                    })
            }
        }
    </script>
@endsection
