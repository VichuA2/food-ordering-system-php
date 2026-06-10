<!DOCTYPE html>
<html>
<head><title>Order Placed</title></head>
<body>
    <h2>Hello {{ $order->customer->name }},</h2>
    <p>Your order #{{ $order->id }} from {{ $order->hotel->name }} has been placed successfully.</p>
    <p><strong>Total Amount:</strong> ${{ number_format($order->total_amount, 2) }}</p>
    <p><strong>Delivery Location:</strong> {{ $order->delivery_location }}</p>
    <p>We will notify you once a delivery person is assigned to your order.</p>
    <br>
    <p>Thank you for using Online Food Ordering!</p>
</body>
</html>
