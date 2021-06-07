@extends('emails.email_template')

@section('title')
    Support ticket #{{ $support_ticket->reference_number }} - {{ $support_ticket->issue }}
@endsection

@section('content')
    {!! $body !!}
@endsection
