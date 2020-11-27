@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('navbar.users') }}</div>

                    <div class="card-body">
                        @include('layouts.table-search', [
                            'route' => route('user.index'),
                            'total' => $users->total()
                        ])

                        <table class="table table-responsive mt-3">
                            <thead>
                                <tr>
                                    <th>{{ __('table.id') }}</th>
                                    <th>{{ __('table.name') }}</th>
                                    <th>{{ __('table.email') }}</th>
                                    <th>{{ __('table.role') }}</th>
                                    <th>{{ __('table.created_at') }}</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>
                                            <a href="{{ route('user.edit', [$user->uuid]) }}">
                                                {{ $user->uuid }}
                                            </a>
                                        </td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->role() }}</td>
                                        <td>{{ $user->created_at->format('d-m-Y') }}</td>
                                        <td>
                                            <a href="{{ route('user.edit', [$user->uuid]) }}" class="btn btn-warning">
                                                E
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection