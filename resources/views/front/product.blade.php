@extends('front.layout.app')
@section('content')
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="/">Home</a></li>
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('front.shop') }}">Shop</a></li>
                    <li class="breadcrumb-item">{{ $product->tittle }}</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="section-7 pt-3 mb-3">
        <div class="container">
            <div class="row ">
                <div class="col-md-5">
                    <div id="product-carousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner bg-light active">
                            {{-- {{$product->product_images}} --}}
                            @if ($product->product_images)
                                @foreach ($product->product_images as $key => $image)
                                    {{-- {{$image->image}} --}}
                                    <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                        <img class="w-100 h-100" src="{{ asset('uploads/product/large/' . $image->image) }}"
                                            alt="Image">
                                    </div>
                                @endforeach
                            @endif


                        </div>
                        <a class="carousel-control-prev" href="#product-carousel" data-bs-slide="prev">
                            <i class="fa fa-2x fa-angle-left text-dark"></i>
                        </a>
                        <a class="carousel-control-next" href="#product-carousel" data-bs-slide="next">
                            <i class="fa fa-2x fa-angle-right text-dark"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="bg-light right">
                        <h1>{{ $product->tittle }}</h1>
                        {{-- <div class="d-flex mb-3">
                            <div class="text-primary mr-2">
                                <small class="fas fa-star"></small>
                                <small class="fas fa-star"></small>
                                <small class="fas fa-star"></small>
                                <small class="fas fa-star-half-alt"></small>
                                <small class="far fa-star"></small>
                            </div>
                            <small class="pt-1">(99 Reviews)</small>
                        </div> --}}
                        @if ($product->compare_price > 0)
                            <h2 class="price text-secondary"><del>${{ $product->compare_price }}</del></h2>
                        @endif

                        <h2 class="price ">${{ $product->price }}</h2>

                        <p>{!! $product->short_description !!}</p>
                        {{-- <a href="javascript:void(0)" onclick="addToCart({{$product->id}})" class="btn btn-dark"><i class="fas fa-shopping-cart"></i> &nbsp;ADD TO CART</a> --}}

                        @if ($product->track_qty == 'Yes')
                            @if ($product->qty > 0)
                                <a class="btn btn-dark" href="javascript:void(0)" onclick="addToCart({{ $product->id }})">
                                    <i class="fa fa-shopping-cart"></i> &nbsp;Add To Cart
                                </a>
                            @else
                                <a class="btn btn-danger">
                                    Out Of Stock
                                </a>
                            @endif
                        @else
                            <a class="btn btn-dark" href="javascript:void(0)" onclick="addToCart({{ $product->id }})">
                                <i class="fa fa-shopping-cart"></i>&nbsp;Add To Cart
                            </a>
                        @endif

                    </div>
                </div>

                <div class="col-md-12 mt-5">
                    <div class="bg-light">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="description-tab" data-bs-toggle="tab"
                                    data-bs-target="#description" type="button" role="tab" aria-controls="description"
                                    aria-selected="true">Description</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="shipping-tab" data-bs-toggle="tab" data-bs-target="#shipping"
                                    type="button" role="tab" aria-controls="shipping" aria-selected="false">Shipping &
                                    Returns</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button id="reviewsBtn" class="nav-link" id="reviews-tab" data-bs-toggle="tab"
                                    data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews"
                                    aria-selected="false">Reviews</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="description" role="tabpanel"
                                aria-labelledby="description-tab">
                                <p>
                                    {!! $product->description !!}
                                </p>
                            </div>
                            <div class="tab-pane fade" id="shipping" role="tabpanel" aria-labelledby="shipping-tab">
                                {!! $product->Shipping_Returns !!}
                            </div>
                            <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                                <div class="col-md-8">
                                    <div class="row">

                                        <form id="reviewsForm">
                                            <h3 class="h4 pb-3">Write a Review</h3>
                                            <div class="form-group col-md-6 mb-3">
                                                <label for="name">Name</label>
                                                <input type="text" class="form-control" name="name" id="name"
                                                    placeholder="Name">
                                                <p></p>
                                            </div>
                                            {{-- <div class="form-group col-md-6 mb-3">
                                                <label for="email">Email</label>
                                                <input type="text" class="form-control" name="email" id="email"
                                                    placeholder="Email">
                                                <p></p>
                                            </div> --}}
                                            <div class="form-group mb-3">
                                                <label for="rating">Rating</label>
                                                <br>
                                                <div class="rating" style="width: 10rem">
                                                    <input id="rating-5" type="radio" name="rating"
                                                        value="5" /><label for="rating-5"><i
                                                            class="fas fa-3x fa-star"></i></label>
                                                    <input id="rating-4" type="radio" name="rating"
                                                        value="4" /><label for="rating-4"><i
                                                            class="fas fa-3x fa-star"></i></label>
                                                    <input id="rating-3" type="radio" name="rating"
                                                        value="3" /><label for="rating-3"><i
                                                            class="fas fa-3x fa-star"></i></label>
                                                    <input id="rating-2" type="radio" name="rating"
                                                        value="2" /><label for="rating-2"><i
                                                            class="fas fa-3x fa-star"></i></label>
                                                    <input id="rating-1" type="radio" name="rating"
                                                        value="1" /><label for="rating-1"><i
                                                            class="fas fa-3x fa-star"></i></label>
                                                </div>
                                                <p class="rating-error text-danger"></p>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="">How was your overall experience?</label>
                                                <textarea name="comment" id="comment" class="form-control" cols="30" rows="10"
                                                    placeholder="How was your overall experience?"></textarea>
                                                <p></p>
                                            </div>
                                            <div>
                                                <button type="submit" class="btn btn-dark">Submit</button>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-5">
                                    <div class="overall-rating mb-3">
                                        <div class="d-flex">
                                            <h1 id="ratingPoints" class="h3 pe-3">0</h1>
                                            <div class="star-rating mt-2" title="70%">
                                                <div class="back-stars">
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>

                                                    <div class="front-stars" id="totalRatings" style="">
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="count" class="pt-2 ps-2"></div>
                                        </div>

                                    </div>
                                    <div id="ratingsDiv">

                                        {{-- <div class="rating-group mb-4">
                                            <span> <strong>Mohit Singh </strong></span>
                                            <div class="star-rating mt-2" title="70%">
                                                <div class="back-stars">
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>

                                                    <div class="front-stars" style="width: 70%">
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="my-3">
                                                <p>I went with the blue model for my new apartment and an very pleased with
                                                    the
                                                    purchase. I'm definitely someone not used to paying this much for
                                                    furniture,
                                                    and I am also anxious about buying online, but I am very happy with the
                                                    quality of this couch. For me, it is the perfect mix of cushy firmness,
                                                    and
                                                    it arrived defect free. It really is well made and hopefully will be my
                                                    main
                                                    couch for a long time. I paid for the extra delivery & box removal, and
                                                    had
                                                    an excellent experience as well. I do tend move my own furniture, but
                                                    with
                                                    an online purchase this expensive, that helped relieved my anxiety about
                                                    having a item this big open up in my space without issues. If you need a
                                                    functional sectional couch and like the feel of leather, this really is
                                                    a
                                                    great choice.

                                                </p>
                                            </div>
                                        </div> --}}
                                    </div>




                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @if (!empty($related_products))
        <section class="pt-5 section-8">
            <div class="container">
                <div class="section-title">
                    <h2>Related Products</h2>
                </div>
                <div class="col-md-12">
                    <div id="related-products" class="carousel">
                        @if (!empty($related_products))
                            @foreach ($related_products as $related_product)
                                @php
                                    $product_images = $related_product->product_images->first();
                                @endphp
                                <div class="card product-card">
                                    <div class="product-image position-relative">
                                        <a href="{{ route('front.product', $related_product->slug) }}">
                                            @if (!empty($product_images->image))
                                                <td><img class="card-img-top"
                                                        src="{{ asset('uploads/product/small/' . $product_images->image) }}"
                                                        class="img-thumbnail"></td>
                                            @else
                                                <td><img class="card-img-top"
                                                        src="{{ asset('admin-assets/img/default-150x150.png') }}"
                                                        class="img-thumbnail"></td>
                                            @endif
                                        </a>

                                        <a class="whishlist" onclick="addToWishList({{ $related_product->id }})"
                                            href="javascript:void(0)"><i class="far fa-heart"></i></a>

                                        <div class="product-action">
                                            @if ($related_product->track_qty == 'Yes')
                                                @if ($related_product->qty > 0)
                                                    <a class="btn btn-dark" href="javascript:void(0)"
                                                        onclick="addToCart({{ $related_product->id }})">
                                                        <i class="fa fa-shopping-cart"></i> Add To Cart
                                                    </a>
                                                @else
                                                    <a class="btn btn-danger">
                                                        Out Of Stock
                                                    </a>
                                                @endif
                                            @else
                                                <a class="btn btn-dark" href="javascript:void(0)"
                                                    onclick="addToCart({{ $related_product->id }})">
                                                    <i class="fa fa-shopping-cart"></i> Add To Cart
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="card-body text-center mt-3">
                                        <a class="h6 link"
                                            href="{{ route('front.product', $related_product->slug) }}">{{ $related_product->tittle }}</a>
                                        <div class="price mt-2">
                                            <span class="h5"><strong>${{ $related_product->price }}</strong></span>
                                            @if ($related_product->compare_price)
                                                <span
                                                    class="h6 text-underline"><del>${{ $related_product->compare_price }}</del></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection
@section('customJs')
    <script type="text/javascript">
        $('#reviewsBtn').click(function(e) {
            e.preventDefault();
            $('#totalRatings').css('width', '90%');

            function showReviws() {
                $('#totalRatings').css('width', '0%');
                $('#ratingsDiv').html('');
                $.ajax({
                    url: "{{ route('front.showRatigs') }}",
                    type: 'GET',
                    data: {
                        'product_id': '{{ $product->id }}',
                    },
                    success: function(response) {
                        if (response.status == true) {
                            var totalRatings = 0;
                            var html = ''
                            var rating = 0;
                            response.reviews.forEach((e) => {
                                let rating = e.rating * 20
                                totalRatings += e.rating;


                                html += `<div class="rating-group mb-4">
                                            <span class="author"><strong>${e.name}</strong></span>
                                            <div class="star-rating mt-2">
                                                <div class="back-stars">
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>

                                                    <div class="front-stars" style="width: ${rating}%">
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="my-3">
                                                <p>${e.comment}</p>
                                            </div>
                                        </div>
                            `

                            });
                            $('#ratingsDiv').append(html)
                            $('#count').html(`(${response.count} Reviews )`)
                            $('#totalRatings').css('width', totalRatings / response.count * 20 + '%');
                            if (response.count != 0) {
                                $('#ratingPoints').html(totalRatings / response.count)
                            }


                        }

                    }

                })
            }
            showReviws()

        })


        $('#reviewsForm').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('front.saveRating', $product->id) }}",
                type: 'POST',
                data: $(this).serializeArray(),
                success: function(response) {
                    if (response.status == false) {
                        if (response.errors == 'plase login') {
                            window.location.href = '{{ route('account.login') }}'
                        }
                        var errors = response.errors;
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
                        if (errors['comment']) {
                            $('#comment').addClass('is-invalid').siblings('p').addClass(
                                'invalid-feedback').html(errors['comment'])
                        } else {
                            $('#comment').removeClass('is-invalid').siblings('p').removeClass(
                                'invalid-feedback').html("")
                        }
                        if (errors['rating']) {
                            $('.rating-error').html(errors['rating'])
                        } else {
                            $('.rating-error').html('')
                        }

                    } else {

                        $('#wishlist_modal .modal-body').html(response.message)
                        $('#wishlist_modal').modal('show')
                        $('#name').val("");
                        $('#email').val("");
                        $('#comment').val("");
                        $('#ratingsDiv').html("")
                        $('#comment').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("")
                        $('#email').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("")
                        $('#name').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("")
                        $('.rating-error').html('')

                        function showReviws() {
                            $('#totalRatings').css('width', '0%');
                            $('#ratingsDiv').html('');
                            $.ajax({
                                url: "{{ route('front.showRatigs') }}",
                                type: 'GET',
                                data: {
                                    'product_id': '{{ $product->id }}',
                                },
                                success: function(response) {
                                    if (response.status == true) {
                                        var totalRatings = 0;
                                        var html = ''
                                        var rating = 0;
                                        response.reviews.forEach((e) => {
                                            let rating = e.rating * 20
                                            totalRatings += e.rating;


                                            html += `<div class="rating-group mb-4">
                                            <span class="author"><strong>${e.name}</strong></span>
                                            <div class="star-rating mt-2">
                                                <div class="back-stars">
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>

                                                    <div class="front-stars" style="width: ${rating}%">
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="my-3">
                                                <p>${e.comment}</p>
                                            </div>
                                        </div>
                            `

                                        });
                                        $('#ratingsDiv').append(html)
                                        $('#count').html(`(${response.count} Reviews )`)
                                        $('#totalRatings').css('width', totalRatings /
                                            response.count * 20 + '%');
                                        if (response.count != 0) {
                                            $('#ratingPoints').html(totalRatings / response
                                                .count)

                                        }


                                    }

                                }

                            })
                        }

                        showReviws()


                    }
                }
            })
        })
    </script>
@endsection
