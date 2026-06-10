<!DOCTYPE html>
<html lang="en">
<head>
    <title>New Order Available to Pick</title>
</head>
<body>
    <h2>Hello Delivery Partner,</h2>
    <p>A new order (#{{$order->id}}) is waiting for delivery!</p>
    <p><strong>Pickup From: </strong>{{$order->hotel->name}}</p>
    <p><strong>Deliver To: </strong>{{$order->delivery_location}}</p>
    <p><strong>Total Amount to Collect: </strong>₹{{number_format($order->total_amount, 2)}}</p>
    <p>Log in to your dashboard quickly to pick up this order before someone else does!</p>
    <br>
    <p>Drive Safe!<br>Food Delivery Team</p>
</body>
</html>