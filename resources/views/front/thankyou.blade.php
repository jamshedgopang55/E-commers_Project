@extends('front.layout.app')
@section('content')
<section class="container">

    <div class="col-md-12 text-center py-5">
        @if(Session::has('success'))
        <div class="alert alert-success">
            {{Session::get('success')}}
        </div>
        @endif
        <h1>Thank You!</h1>
        <p>Your Order Id is ORD#{{$id}}</p>
    </div>
</section>

@endsection
