@extends('front.layout.app')
@section('content')
<main>
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="{{route('front.home')}}">Home</a></li>
                    <a class="breadcrumb-item active" href='{{route('front.shop')}}'>Shop</a>
                </ol>
            </div>
        </div>
    </section>

    <section class="section-6 pt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3 sidebar">
                    <div class="sub-title">
                        <h2>Categories</h3>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="accordion accordion-flush" id="accordionExample">
                                @if (getCategories()->isNotEmpty())

                                  @foreach (getCategories() as $key => $category)
                                 <div class="accordion-item">
                                    @if ($category->subCategory->isNotEmpty())

                                    <h2 class="accordion-header" id="headingOne-{{$key}}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour-{{$key}}" aria-expanded="false" aria-controls="collapseFour-{{$key}}">
                                          {{$category->name}}
                                        </button>
                                    </h2>
                                    @else
                                    <a href="{{ route('front.shop', $category->slug) }}" class="nav-item nav-link {{($categorySelected == $category->id) ? 'text-primary' :  ''}}">{{ $category->name }}</a>
                                    @endif
                                    <div id="collapseFour-{{$key}}" class="accordion-collapse collapse {{($categorySelected == $category->id) ? 'show' :  ''}}" aria-labelledby="headingOne" data-bs-parent="#accordionExample" style="">
                                        <div class="accordion-body">
                                            <div class="navbar-nav">
                                                @if ($category->subCategory->isNotEmpty())
                                                @foreach ($category->subCategory as $subCategory)
                                                <a href="{{route('front.shop',[$category->slug,$subCategory->slug])}}" class="nav-item nav-link {{($SubCategorySelected ==$subCategory->id) ? 'text-primary' :  ''}}"> {{ $subCategory->name }}</a>
                                                @endforeach
                                            @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                @endif

                            </div>
                        </div>
                    </div>

                    <div class="sub-title mt-5">
                        <h2>Brand</h3>
                    </div>

                    <div class="card">
                        <div class="card-body">

                            @if($brands->isNotEmpty())

                            @foreach ($brands as $brand)
                            <div class="form-check mb-2">

                                <input {{(in_array($brand->id,$brandArray)? 'checked' : '')}}  class="form-check-input brand-label" type="checkbox" name='brand[]' value="{{$brand->id}}" id="brand-{{$brand->id}}">
                                <label class="form-check-label " for="brand-{{$brand->id}}">
                                    {{$brand->name}}
                                </label>
                            </div>
                            @endforeach
                            @endif

                        </div>
                    </div>

                    <div class="sub-title mt-5">
                        <h2>Price</h3>
                    </div>

                    <div class="card">
                        <div class="card-body">

                     <input type="text" class="js-range-slider" name="my_range" value="" />
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="row pb-3">
                        <div class="col-12 pb-1">
                            <div class="d-flex align-items-center justify-content-end mb-4">
                                <div class="ml-2">
                                    <select name="sort" id="sort" class="form-control">
                                        <option {{($sort === 'latest') ? 'selected' : ''}} value="latest">Latest</option>
                                        <option {{($sort === 'price_desc') ? 'selected' : ''}} value="price_desc">Price High</option>
                                        <option {{($sort === 'price_asc') ? 'selected' : ''}} value="price_asc">Price Low</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        @if ($products->isNotEmpty())
                        @foreach ($products as $Product)
                            @php
                            $product_images = $Product->product_images->first();
                            @endphp
                        <div class="col-md-4">
                            <div class="card product-card">
                                <a  href="{{route('front.product',$Product->slug)}}">
                                <div class="product-image position-relative">
                                    @if (!empty($product_images->image))
                                    <td><img  class="card-img-top" src="{{ asset('uploads/product/small/' . $product_images->image) }}"
                                            class="img-thumbnail" ></td>
                                @else
                                    <td><img  class="card-img-top" src="{{ asset('admin-assets/img/default-150x150.png') }}"
                                            class="img-thumbnail" ></td>
                                @endif
                                <a class="whishlist"  onclick="addToWishList({{$Product->id}})" href="javascript:void(0)"><i class="far fa-heart"></i></a>


                                <div class="product-action">
                                    @if ($Product->track_qty == 'Yes')
                                        @if ($Product->qty > 0)
                                            <a class="btn btn-dark" href="javascript:void(0)"
                                                onclick="addToCart({{ $Product->id }})">
                                                <i class="fa fa-shopping-cart"></i> Add To Cart
                                            </a>
                                        @else
                                            <a class="btn btn-danger">
                                            Out Of Stock
                                            </a>
                                        @endif
                                    @else
                                        <a class="btn btn-dark" href="javascript:void(0)"
                                            onclick="addToCart({{ $Product->id }})">
                                            <i class="fa fa-shopping-cart"></i> Add To Cart
                                        </a>
                                    @endif

                                </div>
                                </div>
                                <div class="card-body text-center mt-3">
                                    <a class="h6 link" href="{{route('front.product',$Product->slug)}}">{{$Product->tittle}}</a>
                                    <div class="price mt-2">
                                        <span class="h5"><strong>${{$Product->price}}</strong></span>
                                        <span class="h6 text-underline"><del>{{$Product->compare_price}}</del></span>
                                    </div>
                                </div>
                            </a>
                            </div>
                        </div>
                        @endforeach
                        @else
                                <h2 class="text-center">Product Not Available</h2>
                        @endif

                        <div class="col-md-12 pt-5">
                           {{$products->withQueryString()->links('pagination::bootstrap-5')}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>
@endsection
@section('customJs')
    <script>
         $(".js-range-slider").ionRangeSlider({
        type: "double",
        min: 0,
        max: 5000,
        from: {{$priceMin}},
        to: {{$priceMax}},
        grid: true,
        step:10,
        prefix:"$",
        skin : "round",
        max_postfix: "+",
        onFinish : function(){
              apply_filters()
            console.log(slider.result.to)
        }
    });
    let slider  = $(".js-range-slider").data('ionRangeSlider')

        $('.brand-label').change(function(){
            apply_filters()
        });
        $('#sort').change(function(){
            apply_filters()
        });

        function apply_filters(){

            let brands = [];
            $('.brand-label').each(function(){
                if($(this).is(':checked')==true){
                    brands.push($(this).val())
                }
            })

            let url = "{{url()->current()}}?"
            //Brand Filter
            if(brands.length >0 ){
                url+='&brand='+brands.toString()
            }
            //Price Filter
            url+= '&price_min='+slider.result.from+'&price_max='+slider.result.to

            //search
            let keyword = $('#search').val()

            if(keyword.length > 0){
                url+='&search='+keyword;
            }


            //Sorting Filter

            url+= '&sort='+$('#sort').val()

            window.location.href = url;

        }
    </script>
@endsection
