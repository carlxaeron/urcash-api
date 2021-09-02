@component('mail::message')
# Sorry!

Your product is rejected. Please see the remarks below.
### {{$product->remarks[0] ?? ''}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
