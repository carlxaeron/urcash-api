@extends('emails.email_template')

@section('title')
    Cashout successfully approved!
@endsection

@section('content')

    <h3>Hello {{ $first_name }} {{ $last_name }},</h3>
    <div class="offset-md-1 col-md-10 text-center">

    <p>Greetings from G2GBox digital wallet!</p>

    <p>Please be informed that your cashout request was approved and transfer to your account {{ $payment_method }}</p>

    <p>Below are the details of your transaction:</p>

        <ul>
            <li>Transaction Reference No: {{ $ref_number }}</li>
            <li>Transaction Date and Time: {{ $date_time }}</li>
            <li>Ewallet/Bank Name: {{ $payment_method }}</li>
            <li>Account Number: {{ $account_number }}</li>
            <li>Amount: {{ $amount }}</li>
            <li>Fee: {{ $fee }}</li>
            <li>Currency: PHP</li>
        </ul>
        <p>We, at G2GBox thank you for your continued patronage!</p>

    </div>
@endsection
