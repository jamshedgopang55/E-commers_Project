@extends('admin.layout.app')
@section('content')
{{-- @php
    print_r($categories);
    die();
@endphp --}}
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create Sub Category</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{route('sub-Category.index')}}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <form id='subcategoryForm'>
            @csrf
            <!-- Default box -->
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="name">Category</label>
                                    <select name="category" id="category" class="form-control">
                                        @foreach ($categories as $category )
                                        <option {{ ($subCategory->category_id == $category->id) ? 'selected' : '' }} value=" {{ $category->id }}"> {{ $category->name }} </option>
                                        @endforeach
                                    </select>
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="1" value="1" {{( $subCategory->status==1) ? 'selected' : ''}} >Active</option>
                                        <option value="0" value="1" {{( $subCategory->status==0) ? 'selected' : ''}}>Block</option>
                                    </select>
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" value="{{$subCategory->name}}" name="name" id="name" class="form-control"
                                        placeholder="Name">
                                        <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email">Slug</label>
                                    <input readonly  value="{{$subCategory->name}}" type="text" name="slug" id="slug" class="form-control"
                                        placeholder="Slug">
                                       <p></p>
                                </div>
                            </div>
                            {{-- <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="showOnHome">Show On Home</label>
                                    <select name="showOnHome" id="status" class="form-control" id="">
                                        <option value="Yes" {{($subCategory->showOnHome=='Yes') ? 'selected' : ''}}>Yes</option>
                                        <option value="No" {{($subCategory->showOnHome=='No') ? 'selected' : ''}} >No</option>
                                    </select>
                                </div>
                            </div> --}}
                        </div>
                    </div>

                </div>
                <div class="pb-5 pt-3">
                    <button id="btn" class="btn btn-primary">Update</button>
                    <a href="{{route('sub-Category.index')}}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>
            </div>
        </form>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection
@section('customJs')
    <script>
     $(document).ready(function() {
            $('#subcategoryForm').submit('click', function(e) {
                $('#btn').attr('disabled', true)
                const data = $(this).serializeArray()
                let name = document.getElementById('name').value;
                let slug = document.getElementById('slug').value;
                e.preventDefault();
                $.ajax({
                    url: "{{ route('sub-Category.update',$subCategory->id) }}",
                    type: 'PUT',
                    data: data,
                    success: function(response) {
                        $('#btn').attr('disabled', false)
                        if (response['status'] == true) {
                            window.location.href = " {{ route('sub-Category.index') }}"
                            $('#name').removeClass('is-invalid').siblings('p').removeClass(
                                'invalid-feedback').html("")
                            $('#slug').removeClass('is-invalid').siblings('p').removeClass(
                                'invalid-feedback').html("")
                        } else {
                            let errors = response.errors
                            if (errors['name']) {
                                $('#name').addClass('is-invalid').siblings('p').addClass(
                                    'invalid-feedback').html(errors['name'])
                            } else {
                                $('#name').removeClass('is-invalid').siblings('p').removeClass(
                                    'invalid-feedback').html("")
                            }
                            if (errors['category']) {
                                $('#category').addClass('is-invalid').siblings('p').addClass(
                                    'invalid-feedback').html(errors['category'])
                            } else {
                                $('#category').removeClass('is-invalid').siblings('p').removeClass(
                                    'invalid-feedback').html("")
                            }
                            if (errors['status']) {
                                $('#status').addClass('is-invalid').siblings('p').addClass(
                                    'invalid-feedback').html(errors['status'])
                            } else {
                                $('#status').removeClass('is-invalid').siblings('p').removeClass(
                                    'invalid-feedback').html("")
                            }


                            if (errors['slug']) {
                                $('#slug').addClass('is-invalid').siblings('p').addClass(
                                    'invalid-feedback').html(errors['slug'])
                            } else {
                                $('#slug').removeClass('is-invalid').siblings('p').removeClass(
                                    'invalid-feedback').html("")
                            }
                        }

                    }
                })
            })
        })


        //slug genrater
        $('#name').on('input', function() {
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
    </script>
@endsection
