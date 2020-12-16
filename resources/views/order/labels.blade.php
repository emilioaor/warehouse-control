<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Labels</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body {
            overflow: hidden;
            background-color: transparent;
        }
    </style>
</head>
<body>
    <button type="button" class="btn btn-info text-white" onclick="window.print()">
        <i class="fa fa-tag"></i>
        {{ __('form.generateLabels') }}
    </button>
    @for($x = 0; $x < $order->boxes_sum; $x++)
        <div class="whs-labels__card page-break">
            <div class="whs-labels__card-content">
                <div class="whs-labels__customer">{{ $order->customer->description }}</div>
                <div class="whs-labels__courier">
                    <strong>{{ __('validation.attributes.salesOrder') }}:</strong>
                    {{ $order->invoice_number}}
                </div>
                <div class="whs-labels__courier">{{ $order->courier->name }}</div>

                <div class="whs-labels__tag">{{ ($x + 1) }} / {{ $order->boxes_sum }}</div>
            </div>
        </div>
    @endfor
</body>
</html>