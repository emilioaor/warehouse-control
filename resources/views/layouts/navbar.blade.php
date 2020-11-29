<ul class="navbar-nav mr-auto">
    @auth
        @if(Auth::user()->isAdmin())
            @include('layouts.dropdown-menu', [
                'title' => __('navbar.users'),
                'add' => route('user.create'),
                'list' => route('user.index')
            ])
        @endif

        @include('layouts.dropdown-menu', [
            'title' => __('navbar.couriers'),
            'add' => route('courier.create'),
            'list' => route('courier.index')
        ])

        @include('layouts.dropdown-menu', [
            'title' => __('navbar.customers'),
            'add' => route('customer.create'),
            'list' => route('customer.index')
        ])

        @include('layouts.dropdown-menu', [
            'title' => __('navbar.boxes'),
            'add' => route('box.create'),
            'list' => route('box.index')
        ])

        @include('layouts.dropdown-menu', [
            'title' => __('navbar.orders'),
            'add' => route('order.create'),
            'list' => route('order.index')
        ])
    @endauth
</ul>