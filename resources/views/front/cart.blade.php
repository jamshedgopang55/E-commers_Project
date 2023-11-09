@extends('front.layout.app')
@section('content')
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="/">Home</a></li>
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('front.shop') }}">Shop</a></li>
                    <li class="breadcrumb-item">Cart</li>
                </ol>
            </div>
        </div>
    </section>

    <section class=" section-9 pt-4">
        <div class="container">
            <div class="row">
                @if (Session::has('success'))
                    <div class="md-12">
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {!! Session::get('success') !!}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                @endif

                @if (Session::has('error'))
                    <div class="md-12">
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ Session::get('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                @endif
                @if (Cart::count() > 0)
                    <div class="col-md-8">
                        <div class="table-responsive">
                            <table class="table" id="cart">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                        <th>Remove</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($cartContent as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center ">

                                                    @if (!empty($item->options->productImage->image))
                                                        <img class="card-img-top"
                                                            src="{{ asset('uploads/product/small/' . $item->options->productImage->image) }}"
                                                            class="img-thumbnail">
                                                    @else
                                                        <img class="card-img-top"
                                                            src="{{ asset('admin-assets/img/default-150x150.png') }}"
                                                            class="img-thumbnail">
                                                    @endif
                                                    <h2> {{ $item->name }}</h2>
                                                </div>
                                            </td>
                                            <td>$ {{ $item->price }}</td>
                                            <td>
                                                <div class="input-group quantity mx-auto" style="width: 100px;">
                                                    <div class="input-group-btn">
                                                        <button class="btn btn-sm btn-dark btn-minus p-2 pt-1 pb-1 sub"
                                                            data-id="{{ $item->rowId }}">
                                                            <i class="fa fa-minus"></i>
                                                        </button>
                                                    </div>
                                                    <input type="text"
                                                        class="form-control form-control-sm  border-0 text-center"
                                                        value=" {{ $item->qty }}">
                                                    <div class="input-group-btn">
                                                        <button class="btn btn-sm btn-dark btn-plus p-2 pt-1 pb-1 add"
                                                            data-id="{{ $item->rowId }}">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                ${{ $item->price * $item->qty }}
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-danger"
                                                    onclick="deleteItem('{{ $item->rowId }}')"><i
                                                        class="fa fa-times"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach




                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card cart-summery">
                            <div class="sub-title">
                                <h2 class="bg-white">Cart Summery</h2>
                            </div>
                            <div class="card-body">

                                <div class="d-flex justify-content-between summery-end">
                                    <div>SubTotal</div>
                                    <div>${{ Cart::subtotal() }}</div>
                                </div>
                                <div class="pt-3">
                                    <a href="{{ route('account.checkout') }}" class="btn-dark btn btn-block w-100">Proceed
                                        to Checkout</a>
                                </div>
                            </div>
                        </div>



                    </div>
                @else
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body d-flex justify-content">
                                <h4>Your Cart Is Empty!</h4>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
@section('customJs')
    <script>
        $('.add').click(function() {
            var qtyElement = $(this).parent().prev(); // Qty Input
            var qtyValue = parseInt(qtyElement.val());

            if (qtyValue < 10) {
                let rowId = $(this).data('id')

                qtyElement.val(qtyValue + 1);
                let newQty = qtyElement.val()
                updateCart(rowId, newQty);
            }
        });

        $('.sub').click(function() {
            var qtyElement = $(this).parent().next();
            var qtyValue = parseInt(qtyElement.val());
            if (qtyValue > 1) {

                let rowId = $(this).data('id')
                qtyElement.val(qtyValue - 1);
                let newQty = qtyElement.val()

                updateCart(rowId, newQty);
            }
        });

        function updateCart(rowId, qty) {

            $.ajax({
                url: '{{ route('front.updateCart') }}',
                type: 'post',
                dataType: "json",
                data: {
                    rowId,
                    qty
                },
                success: function(response) {
                    window.location.href = '{{ route('front.cart') }}'

                }
            })
        }

        function deleteItem(rowId) {
            $.ajax({
                url: '{{ route('front.deleteCart') }}',
                type: 'post',
                dataType: "json",
                data: {
                    rowId
                },
                success: function(response) {
                    window.location.href = '{{ route('front.cart') }}'

                }
            })
        }
    </script>
@endsection
