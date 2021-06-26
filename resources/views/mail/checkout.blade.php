@component('mail::message')
# Thanks for shopping with us!

Hi {{$user->first_name}} {{$user->last_name}} ,

We received your **#{{$purchases[0]->batch_code}}** on **{{$purchases[0]->created_at}}** and you’ll be paying for this via **{{$purchases[0]->payment_method}}**. 

We’re getting your order ready and will let you know once it’s on the way. We wish you enjoy shopping with us and hope to see you again real soon!
<hr/>

## Delivery Details
**Name:** {{$user->first_name}} {{$user->last_name}}<br/>
**Address:** {{$user->address->complete_address}}<br/>
**Phone:** {{$user->mobile_number}}<br/>
**Email:** {{$user->email}}<br/>
<hr/>

@component('mail::table')
| Product       | Quantity         | Price  |
| ------------- |:-------------:| --------:|
@php
    $subtotal = 0;
@endphp
@foreach ($purchases as $purchase)
@php
    $_total = $purchase->quantity * $purchase->price;
    $subtotal += $_total;
@endphp
| {{$purchase->product->name}} (SOLD BY: {{$purchase->product->owner->last_name}} )    | {{$purchase->quantity}}      | {{ number_format($_total, 2) }}      |
@endforeach
**Subtotal:** {{number_format($subtotal, 2)}}<br/>
**Shipping option:** Standard<br/>
**Paid by:** {{$purchases[0]->payment_method}}<br/>
@endcomponent


Thanks,<br>
{{ config('app.name') }}
@endcomponent
