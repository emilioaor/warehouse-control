@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><i class="fa fa-list"></i> {{ __('navbar.customers') }}</div>

                    <div class="card-body">
                        @include('layouts.table-search', [
                            'route' => route('customer.index'),
                            'total' => $customers->total()
                        ])

                        <table class="table table-responsive mt-3">
                            <thead>
                                <tr>
                                    <th>{{ __('validation.attributes.id') }}</th>
                                    <th>{{ __('validation.attributes.name') }}</th>
                                    <th>{{ __('validation.attributes.email') }}</th>
                                    <th>{{ __('validation.attributes.phone') }}</th>
                                    <th>{{ __('validation.attributes.created_at') }}</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customers as $customer)
                                    <tr>
                                        <td>
                                            <a href="{{ route('customer.edit', [$customer->uuid]) }}">
                                                {{ $customer->uuid }}
                                            </a>
                                        </td>
                                        <td>{{ $customer->description }}</td>
                                        <td>{{ $customer->email }}</td>
                                        <td>{{ $customer->phone }}</td>
                                        <td>{{ $customer->created_at->format('d-m-Y') }}</td>
                                        <td>
                                            <a href="{{ route('customer.edit', $customer->uuid) }}" class="btn btn-warning">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{ $customers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection