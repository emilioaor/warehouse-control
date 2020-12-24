<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('navbar.packingList') }}</title>
    <style>
        .text-center {
            text-align: center;
        }
        body {
            font-size: 18px;
        }
    </style>
</head>
<body>
    <table style="width: 100%">
        <tr>
            <td width="60%"></td>
            <td width="40%" style="text-align: right">
                <img style="width: 250px" src="{{ asset('img/logo.jpeg') }}" alt="">
            </td>
        </tr>
        <tr>
            <td colspan="2" style="font-size: 20px;padding-top: 30px;">
                1701 NW 87 Av suite 300 Doral FL 33122 / <strong>Phone:</strong> 305 614 4478
            </td>
        </tr>
        <tr>
            <td style="padding-top: 30px; font-size: 20px;">
                <strong>{{ __('form.freightForwarder') }}:</strong> {{ $packingList->courier->name }}
            </td>
            <td style="padding-top: 30px;text-align: right;font-size: 20px;">
                <strong>{{ __('form.date') }}:</strong>
                @if($packingList->status === \App\PackingList::STATUS_SENT)
                    {{ $packingList->receivedAtLocal->format('d-m-Y h:i a') }}
                @else
                    {{ $packingList->created_at_local->format('d-m-Y h:i a') }}
                @endif
            </td>
        </tr>
    </table>

    <table border="1" style="width: 100%; margin-top: 20px">
        <thead>
            <tr>
                <th>{{ __('validation.attributes.way') }}</th>
                <th>{{ __('validation.attributes.salesOrder') }}</th>
                <th>{{ __('validation.attributes.customer') }}</th>
                <th>{{ __('validation.attributes.boxes') }}</th>
                <th>{{ __('validation.attributes.size') }}</th>
                <th>{{ __('validation.attributes.weight') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($packingList->orders as $order)
                @foreach($order->orderDetails as $detail)
                    <tr>
                        <td class="text-center">{{ $order->way() }}</td>
                        <td class="text-center">{{ $order->invoice_number }}</td>
                        <td class="text-center">{{ $order->customer->description }}</td>
                        <td class="text-center">{{ $detail->qty }}</td>
                        <td class="text-center">{{ $detail->size }}</td>
                        <td class="text-center">{{ $detail->weight }}</td>
                    </tr>
                @endforeach
            @endforeach

            <tr>
                <th class="text-center">{{ __('form.totals') }}</th>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center">{{ $packingList->boxTotal }}</td>
                <td class="text-center"></td>
                <td class="text-center">{{ $packingList->weightTotal }}</td>
            </tr>
        </tbody>
    </table>

    @if($packingList->status === App\PackingList::STATUS_SENT)
        <table style="width: 100%; margin-top: 20px;">
            <tr>
                <td width="50%" >
                        <p style="height: 30px;">{{ __('validation.attributes.sign') }}</p>
                        <img
                            src="{{ asset('storage/' . $packingList->sign) }}"
                            style="width: 100%; max-height: 160px;"
                            >
                </td>

                @if(isset($packingList->packingListImages[0]))
                    <td width="50%">
                        <p style="height: 30px;">{{ __('validation.attributes.photo') }}</p>
                        <div style="display: flex;justify-content: center;text-align: center;">
                            <img
                                src="{{ asset('storage/' . $packingList->packingListImages[0]->url) }}"
                                style="max-width: 340px; max-height: 160px;"
                            >
                        </div>
                    </td>
                @endif
            </tr>
            @for($x = 1; $x < count($packingList->packingListImages); $x++)
                @if($x === 1 || ($x - 1) % 2 === 0)
                    <tr>
                        <td width="50%">
                            <p style="height: 30px;">{{ __('validation.attributes.photo') }}</p>
                            <img
                                src="{{ asset('storage/' . $packingList->packingListImages[$x]->url) }}"
                                style="width: 100%; max-height: 160px;"
                                >
                        </td>
                        @if(isset($packingList->packingListImages[$x + 1]))
                            <td width="50%">
                                <p style="height: 30px;">{{ __('validation.attributes.photo') }}</p>
                                <img
                                    src="{{ asset('storage/' . $packingList->packingListImages[$x + 1]->url) }}"
                                    style="width: 100%; max-height: 160px;"
                                    >
                            </td>
                        @endif
                    </tr>
                @endif
            @endfor
        </table>



    @endif
</body>
</html>
