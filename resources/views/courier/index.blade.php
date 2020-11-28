@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><i class="fa fa-list"></i> {{ __('navbar.couriers') }}</div>

                    <div class="card-body">
                        @include('layouts.table-search', [
                            'route' => route('courier.index'),
                            'total' => $couriers->total()
                        ])

                        <table class="table table-responsive mt-3">
                            <thead>
                                <tr>
                                    <th>{{ __('validation.attributes.id') }}</th>
                                    <th>{{ __('validation.attributes.name') }}</th>
                                    <th>{{ __('validation.attributes.created_at') }}</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($couriers as $courier)
                                    <tr>
                                        <td>
                                            <a href="{{ route('courier.edit', [$courier->uuid]) }}">
                                                {{ $courier->uuid }}
                                            </a>
                                        </td>
                                        <td>{{ $courier->name }}</td>
                                        <td>{{ $courier->created_at->format('d-m-Y') }}</td>
                                        <td>
                                            <a href="{{ route('courier.edit', $courier->uuid) }}" class="btn btn-warning">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{ $couriers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection