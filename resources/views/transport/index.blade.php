@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><i class="fa fa-list"></i> {{ __('navbar.transports') }}</div>

                    <div class="card-body">
                        @include('layouts.table-search', [
                            'route' => route('transport.index'),
                            'total' => $transports->total()
                        ])

                        <table class="table table-responsive-sm mt-3">
                            <thead>
                                <tr>
                                    <th>{{ __('validation.attributes.name') }}</th>
                                    <th>{{ __('validation.attributes.phone') }}</th>
                                    <th>{{ __('validation.attributes.address') }}</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transports as $transport)
                                    <tr>
                                        <td>{{ $transport->name }}</td>
                                        <td>{{ $transport->phone }}</td>
                                        <td>{{ $transport->address }}</td>
                                        <td>
                                            <a href="{{ route('transport.edit', $transport->uuid) }}" class="btn btn-warning">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{ $transports->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
