@extends('emails.email_template')

@section('title')
    Support ticket #{{ $support_ticket->reference_number }} marked as resolved
@endsection

@section('content')
    <h3>Hello {{ $user }},</h3>
    <div class="offset-md-1 col-md-10 text-center">
        <p>Your request with reference number <strong>{{ $support_ticket->reference_number }}</strong> has now been
            marked as resolved by a customer support agent. If you think your request has not yet been solved, we
            apologize and you may reach us out again at <i>INSERT CUSTOMER SUPPORT EMAIL HERE</i>. Furthermore, you can
            find answers for common inquiries at <i>INSERT FAQ PAGE LINK HERE</i>.</p>
        <p>Please take note that our customer agents will not ask for your password. For suspicious activities, please
            email <i>INSERT CUSTOMER SUPPORT EMAIL HERE</i>.
        </p>
    </div>
@endsection
