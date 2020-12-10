@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><i class="fa fa-list"></i> {{ __('navbar.packingList') }}</div>

                    <div class="card-body">
                        @include('layouts.table-search', [
                            'route' => route('packing-list.index'),
                            'total' => $packingLists->total()
                        ])

                        <table class="table table-responsive mt-3">
                            <thead>
                                <tr>
                                    <th @if(!$packingLists->total()) width="1%" @endif>{{ __('validation.attributes.courier') }}</th>
                                    <th>{{ __('validation.attributes.created_at') }}</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($packingLists as $packingList)
                                    <tr>
                                        <td>{{ $packingList->courier->name }}</td>
                                        <td>{{ $packingList->created_at_local->format('d-m-Y') }}</td>
                                        <td>
                                            <a class="btn btn-warning" href="{{ route('packing-list.edit', $packingList->uuid) }}">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{ $packingLists->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection