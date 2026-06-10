<!DOCTYPE html>
<html lang="en">
<head>
    <title>New Order Received</title>
</head>
<body>
    <h2>Hello {{$order->hotel->name}} Team,</h2>
    <p>You have received a new order #{{$order->id}}</p>
    <p><strong>Customer: </strong>{{$order->customer->name}}</p>
    <p><strong>Total Amount: </strong>{{number_format($order->total_amount,2)}}</p>
    <p>Please prepare the items, A delivery person will pick up soon.</p>
    <br>
    <p>Thank You!</p>
</body>
</html>