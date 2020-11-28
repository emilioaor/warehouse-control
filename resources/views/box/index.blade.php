@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('navbar.boxes') }}</div>

                    <div class="card-body">
                        @include('layouts.table-search', [
                            'route' => route('box.index'),
                            'total' => $boxes->total()
                        ])

                        <table class="table table-responsive mt-3">
                            <thead>
                                <tr>
                                    <th>{{ __('table.id') }}</th>
                                    <th>{{ __('table.description') }}</th>
                                    <th>{{ __('table.size') }}</th>
                                    <th>{{ __('table.weight') }}</th>
                                    <th>{{ __('table.created_at') }}</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($boxes as $box)
                                    <tr>
                                        <td>{{ $box->uuid }}</td>
                                        <td>{{ $box->description }}</td>
                                        <td>{{ $box->size }}</td>
                                        <td>{{ $box->weight }}</td>
                                        <td>{{ $box->created_at->format('d-m-Y') }}</td>
                                        <td>
                                            <a href="{{ route('box.edit', $box->uuid) }}" class="btn btn-warning">
                                                E
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