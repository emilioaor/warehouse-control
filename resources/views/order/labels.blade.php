@extends('layouts.app')

@section('content')
    <div class="container whs-labels">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-tag"></i>
                        {{ __('form.labels') }}
                    </div>

                    <div class="card-body">

                        <div class="row">
                            @foreach($order->orderDetails as $i => $detail)
                                <div class="col-6">
                                    <div class="whs-labels__card">
                                        <div class="whs-labels__customer">{{ $order->customer->description }}</div>
                                        <div class="whs-labels__courier">{{ $order->courier->name }}</div>

                                        <div class="whs-labels__tag">{{ ($i + 1) }} / {{ count($order->orderDetails) }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-12">
                                <button class="btn btn-primary" onclick="window.print()">
                                    <i class="fa fa-print"></i>
                                    {{ __('form.print') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection