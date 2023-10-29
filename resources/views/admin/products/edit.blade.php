@extends('admin.layout.app')
@section('content')
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Product</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('products.list') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <style>
        .copyright {
            display: none
        }
    </style>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            <form action="" id="ProductForm">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="title">Title*</label>
                                            <input type="text" value="{{ $product->tittle }}" name="title"
                                                id="title" class="form-control" placeholder="Title">
                                            <p></p>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="slug">Slug*</label>
                                            <input value="{{ $product->slug }}" type="text" readonly name="slug"
                                                id="slug" class="form-control" placeholder="slug">
                                            <p></p>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="short_description">Short Description</label>
                                            <textarea name="short_description" id="short_description&Returns" cols="30" rows="10" class="summernote"
                                                placeholder="Short Description">{{ $product->short_description }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="description">Description</label>
                                            <textarea value='dfd' name="description" id="description" cols="30" rows="10" class="summernote"
                                                placeholder="Description">{{ $product->description }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="Shipping_Returns">Shipping & Returns</label>
                                            <textarea name="Shipping_Returns" id="Shipping_Returns" cols="30" rows="10" class="summernote"
                                                placeholder="Shipping & Returns">{{ $product->Shipping_Returns }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Media</h2>
                                <div id="image" class="dropzone dz-clickable">
                                    <div class="dz-message needsclick">
                                        <br>Drop files here or click to upload.<br><br>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- /testing --}}
                        <div class="row" id="product-gallery">
                            @if ($productImages->isNotEmpty())
                                @foreach ($productImages as $productImage)
                                    <div class="col-md-3 card img_div" id="image-row-{{ $productImage->id }}">
                                        <img src="{{ asset('uploads/product/small/' . $productImage->image) }}"
                                            class="card-img-top product_image" alt="...">
                                        <div class="card-body">
                                            <a value='{{ $productImage->image }}'
                                                onclick="deleteOldImage({{ $productImage->id }})"
                                                class="btn btn-danger">Delete</a>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        {{-- /testing --}}
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Pricing</h2>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="price">Price*</label>
                                            <input type="text" name="price" id="price" class="form-control"
                                                value="{{ $product->price }}" placeholder="Price">
                                            <p></p>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="compare_price">Compare at Price</label>
                                            <input value="{{ $product->compare_price }}" type="text"
                                                name="compare_price" id="compare_price" class="form-control"
                                                placeholder="Compare Price">
                                            <p class="text-muted mt-3">
                                                To show a reduced price, move the product’s original price into Compare at
                                                price. Enter a lower value into Price.
                                            </p>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Inventory</h2>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="sku">SKU (Stock Keeping Unit)*</label>
                                            <input type="text" value="{{ $product->sku }}" name="sku"
                                                id="sku" class="form-control" placeholder="sku">
                                            <p></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="barcode">Barcode</label>
                                            <input type="text" value="{{ $product->barcode }}" name="barcode"
                                                id="barcode" class="form-control" placeholder="Barcode">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <div class="custom-control custom-checkbox">
                                                <input type="text" name="track_qty" value="No" hidden>
                                                <input class="custom-control-input" type="checkbox" id="track_qty"
                                                    value="Yes" name="track_qty"
                                                    {{ $product->track_qty == 'Yes' ? 'checked' : '' }}>
                                                <p></p>
                                                <label for="track_qty" class="custom-control-label">Track Quantity</label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <input type="number" value="{{ $product->qty }}" min="0"
                                                name="qty" id="qty" class="form-control" placeholder="Qty">
                                            <p></p>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Related Products</h2>
                                <div class="mb-3">
                                    <select multiple class="related_products w-100" name="related_products[]"
                                        id="related_products">
                                        @if(isset($related_products))
                                        @if ($related_products->isNotEmpty())
                                            @foreach ($related_products as $related_product)
                                            <option selected value="{{$related_product->id}}">{{$related_product->tittle}}</option>
                                            @endforeach
                                        @endif
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Product status</h2>
                                <div class="mb-3">
                                    <select name="status" id="status" class="form-control">
                                        <option value="1" {{ $product->status == 1 ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="0" {{ $product->status == 0 ? 'selected' : '' }}>Block
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h2 class="h4  mb-3">Product category</h2>
                                <div class="mb-3">
                                    <label for="category">Category</label>
                                    <select name="category" id="category" class="form-control">
                                        <option value="">Please Select Category</option>
                                        @if ($categories->isNotEmpty())
                                            {
                                            @foreach ($categories as $category)
                                                <option {{ $product->category_id == $category->id ? 'selected' : '' }}
                                                    value="{{ $category->id }}"> {{ $category->name }}</option>
                                            @endforeach
                                            }
                                        @endif
                                        <p></p>
                                    </select>
                                    <p></p>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="category">Sub category</label>
                                    <select name="sub_category" id="sub_category" class="form-control">
                                        <option value="">Please Select Sub Category</option>
                                        @if ($subCategories->isNotEmpty())
                                            {

                                            @foreach ($subCategories as $subCategory)
                                                <option
                                                    {{ $product->sub_category_id == $subCategory->id ? 'selected' : '' }}
                                                    value="{{ $subCategory->id }}"> {{ $subCategory->name }}</option>
                                            @endforeach
                                            }
                                        @endif
                                    </select>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Product brand</h2>
                                <div class="mb-3">
                                    <select name="brand" id="brand" class="form-control">
                                        <option value="">Please Select Brand</option>
                                        @if ($brands->isNotEmpty())
                                            {

                                            @foreach ($brands as $brand)
                                                <option {{ $brand->id == $product->brand_id ? 'selected' : '' }}
                                                    value="{{ $brand->id }}"> {{ $brand->name }}</option>
                                            @endforeach
                                            }
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Featured product</h2>
                                <div class="mb-3">
                                    <select name="featured" id="featured" class="form-control">
                                        <option value="Yes" {{ $product->is_featured == 'Yes' ? 'selected' : '' }}>Yes
                                        </option>
                                        <option value="No" {{ $product->is_featured == 'No' ? 'selected' : '' }}>No
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>



                    </div>
                </div>
                <div class="pb-5 pt-3">
                    <button type="submit" id="btn" class="btn btn-primary">Update</button>
                    <a href="{{ route('products.list') }}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>

        </div>
        </form>
        <!-- /.card -->
    </section>
@endsection
@section('customJs')
    <script>
        $('#related_products').select2({
            ajax: {
                url: '{{ route('front.getProducts') }}',
                dataType: 'json',
                tags: true,
                multiple: true,
                minimumInputLength: 3,
                processResults: function(data) {
                    return {

                        results: data.tags
                    };
                }
            }
        });

        function deleteOldImage(id) {
            $.ajax({
                url: "{{ route('product.imageDelete') }}",
                type: 'POST',
                data: {
                    id: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log(response)
                    if (response['status'] == true) {
                        $('#image-row-' + id).remove();
                    }
                }
            })
        }
        $(document).ready(function() {
            $(".summernote").summernote({
                height: 250
            })
            $('#ProductForm').submit('click', function(e) {
                $('#btn').attr('disabled', true)
                const data = $(this).serializeArray()
                e.preventDefault();
                $.ajax({
                    url: "{{ route('product.update', $product->id) }}",
                    type: 'POST',
                    data: data,

                    success: function(response) {
                        $('#btn').attr('disabled', false)

                        if (response['status'] == true) {
                            window.location.href = " {{ route('products.list') }}"
                            $('#name').removeClass('is-invalid').siblings('p').removeClass(
                                'invalid-feedback').html("")
                            $('#slug').removeClass('is-invalid').siblings('p').removeClass(
                                'invalid-feedback').html("")
                        } else {
                            let errors = response.errors
                            if (errors['title']) {
                                $('#title').addClass('is-invalid').siblings('p').addClass(
                                    'invalid-feedback').html(errors['title'])
                            } else {
                                $('#title').removeClass('is-invalid').siblings('p').removeClass(
                                    'invalid-feedback').html("")
                            }
                            if (errors['status']) {
                                $('#status').addClass('is-invalid').siblings('p').addClass(
                                    'invalid-feedback').html(errors['status'])
                            } else {
                                $('#status').removeClass('is-invalid').siblings('p')
                                    .removeClass(
                                        'invalid-feedback').html("")
                            }
                            if (errors['price']) {
                                $('#price').addClass('is-invalid').siblings('p').addClass(
                                    'invalid-feedback').html(errors['price'])
                            } else {
                                $('#price').removeClass('is-invalid').siblings('p')
                                    .removeClass(
                                        'invalid-feedback').html("")
                            }

                            if (errors['slug']) {
                                $('#slug').addClass('is-invalid').siblings('p').addClass(
                                    'invalid-feedback').html(errors['slug'])
                            } else {
                                $('#slug').removeClass('is-invalid').siblings('p').removeClass(
                                    'invalid-feedback').html("")
                            }

                            if (errors['sku']) {
                                $('#sku').addClass('is-invalid').siblings('p').addClass(
                                    'invalid-feedback').html(errors['sku'])
                            } else {
                                $('#sku').removeClass('is-invalid').siblings('p').removeClass(
                                    'invalid-feedback').html("")
                            }
                            if (errors['qty']) {
                                $('#qty').addClass('is-invalid').siblings('p').addClass(
                                    'invalid-feedback').html(errors['qty'])
                            } else {
                                $('#qty').removeClass('is-invalid').siblings('p').removeClass(
                                    'invalid-feedback').html("")
                            }
                            if (errors['category']) {
                                $('#category').addClass('is-invalid').siblings('p').addClass(
                                    'invalid-feedback').html(errors['category'])
                            } else {
                                $('#category').removeClass('is-invalid').siblings('p')
                                    .removeClass(
                                        'invalid-feedback').html("")
                            }
                        }

                    }
                })
            })
        })


        //slug genrater
        $('#title').on('input', function() {
            element = $(this)
            tittle = element.val()
            $.ajax({
                url: "{{ route('getSlug') }}",
                type: 'GET',
                data: {
                    title: element.val()
                },
                success: function(response) {
                    if (response['status'] == true) {
                        $('#slug').val(response['slug'])
                    }
                }
            })
        })

        //Sub category
        $('#category').change(function() {
            let category_id = $(this).val()
            $.ajax({
                url: "{{ route('product.subCategory.index') }}",
                type: 'GET',
                data: {
                    category_id: category_id
                },
                success: function(response) {
                    if (response['status'] == true) {
                        $('#sub_category').find('option').not(':first').remove();
                        $.each(response['subCategories'], function(key, item) {
                            $('#sub_category').append(
                                `<option value='${item.id}'>${item.name}</option>`)
                        })
                    }
                }
            })
        })


        Dropzone.autoDiscover = false;
        $(function() {
            // Summernote
            $('.summernote').summernote({
                height: '320px'
            });
            const dropzone = $("#image").dropzone({
                url: "{{ route('temp-images.create') }}",
                maxFiles: 1,
                paramName: 'image',
                addRemoveLinks: true,
                acceptedFiles: "image/jpeg,image/png,image/gif",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(file, response) {
                    let html = `<div class="col-md-3 card img_div" id="image-row-${response.image_id}">
                                    <img src="${response.image_path}"  class="card-img-top product_image" alt="...">
                                    <input type="text" hidden name="images_array[]" value="${response.image_id}">
                                    <div class="card-body">
                                        <a  onclick="deleteImae(${response.image_id})" class="btn btn-danger">Delete</a>
                                    </div>
                                    </div>`;
                    $('#product-gallery').append(html)

                },
                complete: function(file) {
                    this.removeFile(file)
                }
            });
        });

        function deleteImae(id) {
            $('#image-row-' + id).remove();

        }
    </script>
@endsection
