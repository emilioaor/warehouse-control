<ul class="navbar-nav mr-auto">
    @auth
        @include('layouts.dropdown-menu', [
            'title' => __('navbar.orders'),
            'links' => [
                [
                    'label' => __('navbar.addNew'),
                    'route' => route('order.create')
                ],
                [
                    'label' => __('navbar.list'),
                    'route' => route('order.index')
                ],
                [
                    'label' => __('navbar.report'),
                    'route' => route('order.report')
                ]
            ]
        ])

        @include('layouts.dropdown-menu', [
            'title' => __('navbar.packingList'),
            'links' => [
                [
                    'label' => __('navbar.addNew'),
                    'route' => route('packing-list.create')
                ],
                [
                    'label' => __('navbar.list'),
                    'route' => route('packing-list.index')
                ],
                [
                    'label' => __('navbar.report'),
                    'route' => route('packing-list.report')
                ]
            ]
        ])

        @if(Auth::user()->isAdmin())
            @include('layouts.dropdown-menu', [
                'title' => __('navbar.users'),
                'links' => [
                    [
                        'label' => __('navbar.addNew'),
                        'route' => route('user.create')
                    ],
                    [
                        'label' => __('navbar.list'),
                        'route' => route('user.index')
                    ]
                ]
            ])
        @endif

        @include('layouts.dropdown-menu', [
            'title' => __('navbar.couriers'),
            'links' => [
                [
                    'label' => __('navbar.addNew'),
                    'route' => route('courier.create')
                ],
                [
                    'label' => __('navbar.list'),
                    'route' => route('courier.index')
                ]
            ]
        ])

        @include('layouts.dropdown-menu', [
            'title' => __('navbar.customers'),
            'links' => [
                [
                    'label' => __('navbar.addNew'),
                    'route' => route('customer.create')
                ],
                [
                    'label' => __('navbar.list'),
                    'route' => route('customer.index')
                ]
            ]
        ])

        @include('layouts.dropdown-menu', [
            'title' => __('navbar.boxes'),
            'links' => [
                [
                    'label' => __('navbar.addNew'),
                    'route' => route('box.create')
                ],
                [
                    'label' => __('navbar.list'),
                    'route' => route('box.index')
                ]
            ]
        ])
    @endauth
</ul>