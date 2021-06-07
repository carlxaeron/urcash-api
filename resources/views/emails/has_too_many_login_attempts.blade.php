@extends('emails.email_template')

@section('title')
    Your account has been temporarily locked
@endsection

@section('content')
    <h3>Hello, {{ $first_name }} {{ $last_name }}!</h3>
    <div class="offset-md-1 col-md-10 text-center">
        <p>This is to inform you that your account with mobile number <strong>{{ $mobile_number }}</strong> has been
            temporarily locked at our site due to numerous failed login attempts. If you forgot your password, you may
            visit <i>INSERT LINK TO FORGOT PASSWORD PAGE HERE</i> to change your password.
        </p>
        <p>Please take note that we will not ask for your password. For suspicious activities, please email
            <i>INSERT CUSTOMER SUPPORT EMAIL HERE</i>.
        </p>
    </div>
@endsection
