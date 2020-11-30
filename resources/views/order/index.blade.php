@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><i class="fa fa-list"></i> {{ __('navbar.orders') }}</div>

                    <div class="card-body">
                        @include('layouts.table-search', [
                            'route' => route('order.index'),
                            'total' => $orders->total()
                        ])

                        <table class="table table-responsive mt-3">
                            <thead>
                                <tr>
                                    <th width="20%">{{ __('validation.attributes.id') }}</th>
                                    <th width="20%">{{ __('validation.attributes.customer') }}</th>
                                    <th width="20%">{{ __('validation.attributes.courier') }}</th>
                                    <th width="20%">{{ __('validation.attributes.created_at') }}</th>
                                    <th width="15%">{{ __('validation.attributes.status') }}</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>
                                            <a href="{{ route('order.edit', $order->uuid) }}">
                                                {{ $order->uuid }}
                                            </a>
                                        </td>
                                        <td>{{ $order->customer->description }}</td>
                                        <td>{{ $order->courier->name }}</td>
                                        <td>{{ $order->date->format('d-m-Y') }}</td>
                                        <td>{!! $order->statusHtml() !!}</td>
                                        <td>
                                            <a href="{{ route('order.edit', $order->uuid) }}" class="btn btn-warning">
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