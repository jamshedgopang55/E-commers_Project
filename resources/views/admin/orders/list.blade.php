@extends('admin.layout.app')
@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Orders</h1>
                </div>
                <div class="col-sm-6 text-right">
                    {{-- <a href="{{ route('order.create') }}" class="btn btn-primary">New order</a> --}}
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            <div class="card">
                @include('admin.message')
                <form action="">
                    <div class="card-header">
                        <div class="card-title">
                            <button class="btn btn-default btn-sm" type="button"
                                onclick="window.location.href='{{ route('orders.index') }}'">Reset</button>
                        </div>
                        <div class="card-tools">
                            <div class="input-group input-group" style="width: 250px;">
                                <input type="text" value="{{ Request::get('keyword') }}" name="keyword"
                                    class="form-control float-right" placeholder="Search">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th width="60">Order#</th>
                                <th>Name</th>
                                <th>email</th>
                                <th>Phone</th>
                                <th width="100">Status</th>
                                <th width="100">Grand Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($orders->isNotEmpty())
                                @foreach ($orders as $order)
                                    <tr>
                                        <td><a href="{{route('orders.detail',$order->id)}}">ORD#{{ $order->id }}</a></td>
                                        <td>{{ $order->name }}</td>
                                        <td>{{ $order->email }}</td>
                                        <td>{{ $order->mobile }}</td>
                                        <td>
                                            @if($order->status == 'pending')
                                            <span class="badge bg-danger">Pending</span>
                                            @elseif ($order->status == 'shipped')
                                            <span class="badge bg-info">Shipped</span>
                                            @elseif($order->status =='cancelled')
                                            <span class="badge bg-danger">cancelled</span>
                                            @else
                                            <span class="badge bg-success">Delivered</span>
                                            @endif
                                        </td>
                                        <td>${{ number_format($order->grand_total,2) }}</td>
                                @endforeach

                            @else
                                <tr>
                                    <td colspan="5">
                                        Records Not Found
                                    </td>
                                </tr>
                            @endif

                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    {{ $orders->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection
@section('customJs')
    <script>

    </script>
@endsection
