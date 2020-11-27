<ul class="navbar-nav mr-auto">
    @auth
        @if(Auth::user()->isAdmin())
            @include('layouts.dropdown-menu', [
                'title' => __('navbar.users'),
                'add' => route('logout'),
                'list' => route('user.index')
            ])
        @endif

        @include('layouts.dropdown-menu', [
            'title' => __('navbar.couriers'),
            'add' => route('logout'),
            'list' => route('logout')
        ])

        @include('layouts.dropdown-menu', [
            'title' => __('navbar.customers'),
            'add' => route('logout'),
            'list' => route('logout')
        ])

        @include('layouts.dropdown-menu', [
            'title' => __('navbar.boxes'),
            'add' => route('logout'),
            'list' => route('logout')
        ])

        @include('layouts.dropdown-menu', [
            'title' => __('navbar.orders'),
            'add' => route('logout'),
            'list' => route('logout')
        ])
    @endauth
</ul>