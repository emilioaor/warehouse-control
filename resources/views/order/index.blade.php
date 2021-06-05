@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><i class="fa fa-list"></i> {{ __('navbar.orders') }}</div>

                    <div class="card-body">
                        @include('layouts.table-search', [
                            'route' => Auth::user()->isSeller() ? route('seller.order.index') : route('order.index'),
                            'total' => $orders->total()
                        ])

                        <table class="table table-responsive mt-3">
                            <thead>
                                <tr>
                                    <th @if(!$orders->total()) width="1%" @endif>{{ __('validation.attributes.customer') }}</th>
                                    <th>{{ __('validation.attributes.courier') }}</th>
                                    <th>{{ __('validation.attributes.salesOrder') }}</th>
                                    <th>{{ __('validation.attributes.created_at') }}</th>
                                    <th>{{ __('validation.attributes.status') }}</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>{{ $order->customer->description }}</td>
                                        <td>{{ $order->courier->name }}</td>
                                        <td>{{ $order->invoice_number }}</td>
                                        <td>{{ $order->date_local->format('d-m-Y h:i a') }}</td>
                                        <td>{!! $order->statusHtml() !!}</td>
                                        <td>
                                            <a
                                                href="{{ Auth::user()->isSeller() ? route('seller.order.edit', $order->uuid) : route('order.edit', $order->uuid) }}"
                                                class="btn btn-warning"
                                            >
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>

                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
