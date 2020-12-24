@component('mail::message')
# {{ __('email.orderCreated') }}

{{ __('email.orderCreated.message') }}

**{{ __('validation.attributes.customer') }}:** {{ $order->customer->description }}

**{{ __('validation.attributes.courier') }}:** {{ $order->courier->name }}

**{{ __('validation.attributes.salesOrder') }}:** {{ $order->invoice_number }}

**{{ __('validation.attributes.way') }}:** {{ $order->way() }}

----------------------------------------------
@foreach($order->orderDetails as $i => $detail)
**{{ $i + 1 }}.** {{ $detail->description }} / {{ $detail->size }} / {{ $detail->weight }} / {{ $detail->qty }} {{ __('validation.attributes.boxes') }}

@endforeach
----------------------------------------------

@if($order->comment)
**{{ __('validation.attributes.comment') }}:**

{{ $order->comment }}
@endif
@endcomponent
