<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order Email</title>
</head>

<body style="font-family: Arial, Helvetica, sans-serif; font-size:16px">


    @if ($mailData['order']->userTpye == 'customer')
        <h1>Thanks for your order!!</h1>
        <p>Your Order Id Is #{{ $mailData['order']->id }}</p>
    @else
        <h1>You have received an order:{{ $mailData['order']->id }} </h1>
        <p>Order Id #{{ $mailData['order']->id }}</p>
    @endif


    <h2>Products</h2>
    <table style="background: #ccc">
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($mailData['order']->items as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>${{ number_format($item->price, 2) }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>${{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach

            <tr>
                <th colspan="3" align="right">Subtotal:</th>
                <td>${{ number_format($mailData['order']->subtotal, 2) }}</td>
            </tr>

            <tr>
                <th colspan="3" align="right">Shipping:</th>
                <td>${{ number_format($mailData['order']->shipping, 2) }}</td>
            </tr>
            <tr>
                <th colspan="3" align="right">Discount
                    {{ !empty($mailData['order']->coupon_code) ? '(' . $mailData['order']->coupon_code . ')' : '' }}:
                </th>
                <td>${{ number_format($mailData['order']->discount, 2) }}</td>
            </tr>
            <tr>
                <th colspan="3" align="right">Grand Total:</th>
                <td>${{ number_format($mailData['order']->grand_total, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>
