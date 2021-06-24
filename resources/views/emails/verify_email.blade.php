@extends('emails.email_template')

@section('title')
    Confirm your Registration
@endsection

@section('content')
    <h3>Hello, {{ $first_name }} {{ $last_name }}!</h3>
    <div class="offset-md-1 col-md-10 text-center">
        <p>Thank you for registering at {{ config('app.name') }}! We are glad to have you on-board with us. To verify your email, please enter the code below:</p>
        <div class="container" style="margin: 12px; text-align: center;">
            <span class="circle-bordered-dark-blue" id="verification-code"><strong>{{ $code }}</strong></span>
        </div>

        <p>Do not share this code with anyone!</p>
    </div>
@endsection
