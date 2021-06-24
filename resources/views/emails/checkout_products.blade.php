@extends('emails.email_template')

@section('title')
    Checkout successful!
@endsection

@section('content')
    <h3>Hello {{ $first_name }} {{ $last_name }},</h3>
    <div class="offset-md-1 col-md-10 text-center">
        <p>This is to inform you that a customer with mobile number <strong>{{ $customer_mobile_number }}</strong> has
            purchased products from your shop. Listed below are the product(s) purchased from
            <strong>{{ $business_name }}</strong>:</p>
        <ul>
        @foreach ($products as $product)
            <li>EAN: {{ $product['ean'] }}</li>
            <li>Product: {{ $product['name'] }}</li>
            <li>Manufacturer: {{ $product['manufacturer'] }}</li>
            <li>Quantity: {{ $product['quantity'] }}</li><br />
        @endforeach
        </ul>
        <p>Subtotal: &#8369; {{ $subtotal }}</p>
        <p>We, at {{ config('app.name') }} thank you for your continued patronage!</p>
    </div>
@endsection
