@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><i class="fa fa-list"></i> {{ __('navbar.boxes') }}</div>

                    <div class="card-body">
                        @include('layouts.table-search', [
                            'route' => route('box.index'),
                            'total' => $boxes->total()
                        ])

                        <table class="table table-responsive mt-3">
                            <thead>
                                <tr>
                                    <th @if(!$boxes->total()) width="1%" @endif>{{ __('validation.attributes.description') }}</th>
                                    <th>{{ __('validation.attributes.size') }}</th>
                                    <th>{{ __('validation.attributes.weight') }}</th>
                                    <th>{{ __('validation.attributes.created_at') }}</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($boxes as $box)
                                    <tr>
                                        <td>{{ $box->description }}</td>
                                        <td>{{ $box->size }}</td>
                                        <td>{{ $box->weight }}</td>
                                        <td>{{ $box->created_at_local->format('d-m-Y') }}</td>
                                        <td>
                                            <a href="{{ route('box.edit', $box->uuid) }}" class="btn btn-warning">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{ $boxes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection