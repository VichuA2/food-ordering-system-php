<!DOCTYPE html>
<html>
<head><title>Delivery Person Assigned</title></head>
<body>
    <h2>Hello {{ $order->customer->name }},</h2>
    <p>A delivery person has been assigned to your order #{{ $order->id }} from {{ $order->hotel->name }}.</p>
    
    <h3>Delivery Person Details:</h3>
    <p><strong>Name:</strong> {{ $order->deliveryPerson->name }}</p>
    <p><strong>Contact/Email:</strong> {{ $order->deliveryPerson->email }}</p>
    
    <p>They will contact you soon when they arrive at {{ $order->delivery_location }}.</p>
    <br>
    <p>Thank you!</p>
</body>
</html>
