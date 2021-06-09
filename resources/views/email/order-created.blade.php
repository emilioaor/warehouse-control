@component('mail::message')
# {{ __('email.order') }} \#{{ $order->id }}

{{ __('email.orderCreated.message') }}

**{{ __('validation.attributes.customer') }}:** {{ $order->customer->description }}

**{{ __('validation.attributes.courier') }}:** {{ $order->courier->name }}

**{{ __('validation.attributes.salesOrder') }}:** {{ $order->invoice_number }}

**{{ __('validation.attributes.way') }}:** {{ $order->way() }}

<table class="table" border="1" style="width: 100%;">
    <thead>
        <tr>
            <th>{{ __('validation.attributes.description') }}</th>
            <th>{{ __('validation.attributes.size') }}</th>
            <th>{{ __('validation.attributes.weight') }}</th>
            <th>{{ __('validation.attributes.qty') }}</th>
            <th>{{ $order->way === 'airway' ? __('validation.attributes.volumetricWeight') : __('validation.attributes.cubicFeet') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->orderDetails as $i => $detail)
            <tr>
                <td>{{ $detail->description }}</td>
                <td style="text-align: center">{{ str_replace('*', '*', $detail->size) }}</td>
                <td style="text-align: center">{{ $detail->weight }}</td>
                <td style="text-align: center">{{ $detail->qty }}</td>
                <td style="text-align: center">{{ $detail->volumetricWeight() }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@if($order->comment)
**{{ __('validation.attributes.comment') }}:**

{{ $order->comment }}
@endif
@endcomponent
