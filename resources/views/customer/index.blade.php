@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('navbar.customers') }}</div>

                    <div class="card-body">
                        @include('layouts.table-search', [
                            'route' => route('customer.index'),
                            'total' => $customers->total()
                        ])

                        <table class="table table-responsive mt-3">
                            <thead>
                                <tr>
                                    <th>{{ __('table.id') }}</th>
                                    <th>{{ __('table.name') }}</th>
                                    <th>{{ __('table.email') }}</th>
                                    <th>{{ __('table.phone') }}</th>
                                    <th>{{ __('table.created_at') }}</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customers as $customer)
                                    <tr>
                                        <td>{{ $customer->uuid }}</td>
                                        <td>{{ $customer->description }}</td>
                                        <td>{{ $customer->email }}</td>
                                        <td>{{ $customer->phone }}</td>
                                        <td>{{ $customer->created_at->format('d-m-Y') }}</td>
                                        <td>
                                            <a href="{{ route('customer.edit', $customer->uuid) }}" class="btn btn-warning">
                                                E
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