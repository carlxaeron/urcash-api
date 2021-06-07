@extends('emails.email_template')

@section('title')
    Verification request #{{ $verification_request->id }} was {{ $status }}
@endsection

@section('content')
    <h3>Hello {{ $first_name }} {{ $last_name }},</h3>
    <div class="offset-md-1 col-md-10 text-center">
    @if ($verification_request->type == 'merchant_verification')
        @if ($status == 'accepted')
            <p>Good day! You are now successfully verified as a merchant.</p>
        @elseif ($status == 'rejected')
            <p>Good day. We are sorry to inform you that your verification request for your merchant account is
                rejected. Don't be discouraged as you may apply for a merchant verification at any time.</p>
        @endif
    @elseif ($verification_request->type == 'product_verification')
        @if ($status == 'accepted')
            <p>Good day! We have now successfully verified your product with the following details:</p>
            <ul>
                <li>EAN: {{ $ean }}</li>
                <li>Product: {{ $product_name }}</li>
                <li>Manufacturer: {{ $product_manufacturer }}</li>
                <li>Variant: {{ $variant }}</li>
            </ul>
        @elseif ($status == 'rejected')
            <p>Good day. We are sorry to inform you that your product was rejected as it may have violated our Terms
                and Conditions and/or Privacy Policy rules. We strictly encourage all our users to follow our rules
                and regulations accordingly.
            </p>
        @endif
    @elseif ($verification_request->type == 'wallet_verification')
        <p>Good day! Your wallet verification request with reference number <strong>{{ $verification_request->id }}</strong>
            was {{ $status }}.</p>
        @if ($status == 'accepted')
            <p>At level {{ $verification_request->level }},
            @if ($verification_request->level == 1)
                you may now accept payments via QR codes and transact for up to 50,000 per month.
            @elseif ($verification_request->level == 2)
                your transaction limit has been increased to 100,000 per month. This is for cash-ins and cash-outs.
            @elseif ($verification_request->level == 3)
                your transaction limit has been increased to 500,000 per month. This is for cash-ins and cash-outs.
            @endif
            </p>
        @elseif ($status == 'rejected')
            <p>You may submit another wallet verification request to increase your level limits. We encourage our users
                to submit authentic and correct legal documents for their wallet verification requests to be accepted.
            </p>
        @endif
    @endif
        <p>Thank you for your continued patronage!</p>
    </div>
@endsection
