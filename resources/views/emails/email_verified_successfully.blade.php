@extends('emails.email_template')

@section('title')
    You have successfully verified your email!
@endsection

@section('content')
    <h3>Hello, {{ $first_name }} {{ $last_name }}!</h3>
    <div class="offset-md-1 col-md-10 text-center">
        <p>Good day! This is to inform you that you have successfully verified your email. Thank you for your continued
            patronage and we hope to see you on the platform real soon!</p>
    </div>
@endsection
