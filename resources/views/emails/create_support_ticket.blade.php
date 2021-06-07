@extends('emails.email_template')

@section('title')
    Support ticket #{{ $support_ticket->reference_number }} has been created
@endsection

@section('content')
    <h3>Hello {{ $user }},</h3>
    <div class="offset-md-1 col-md-10 text-center">
        <p>Thank you for reaching out to Customer Support. We have received your request and your reference number is
            <strong>{{ $support_ticket->reference_number }}</strong>. Your email was used to submit this request.
            If you did not submit this request, please let us know at <i>INSERT CUSTOMER SUPPORT EMAIL HERE</i>.
        </p>
        <p>This is an automated reply and our customer support agents will get back to you shortly. You can find
            answers for common inquiries at <i>INSERT FAQ PAGE LINK HERE</i>.</p>
        <p>Please take note that our customer agents will not ask for your password. For suspicious activities, please
            email <i>INSERT CUSTOMER SUPPORT EMAIL HERE</i>.
        </p>
    </div>
@endsection
