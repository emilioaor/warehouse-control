<ul class="navbar-nav mr-auto">
    @auth
        @include('layouts.dropdown-menu', [
            'show' => ! Auth::user()->isSeller(),
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
                'show' => Auth::user()->isSeller(),
                'title' => __('navbar.orders'),
                'links' => [
                    [
                        'label' => __('navbar.list'),
                        'route' => route('seller.order.index')
                    ],
                    [
                        'label' => __('navbar.report'),
                        'route' => route('seller.order.report')
                    ]
                ]
            ])

        @include('layouts.dropdown-menu', [
            'show' => ! Auth::user()->isSeller(),
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

        @include('layouts.dropdown-menu', [
            'title' => __('navbar.users'),
            'show' => Auth::user()->isAdmin(),
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

        @include('layouts.dropdown-menu', [
            'show' => ! Auth::user()->isSeller(),
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
            'show' => ! Auth::user()->isSeller(),
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
                'show' => Auth::user()->isSeller(),
                'title' => __('navbar.customers'),
                'links' => [
                    [
                        'label' => __('navbar.list'),
                        'route' => route('seller.customer.index')
                    ]
                ]
            ])

        @include('layouts.dropdown-menu', [
            'show' => ! Auth::user()->isSeller(),
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

        @include('layouts.dropdown-menu', [
            'show' => ! Auth::user()->isSeller(),
            'title' => __('navbar.transports'),
            'links' => [
                [
                    'label' => __('navbar.addNew'),
                    'route' => route('transport.create')
                ],
                [
                    'label' => __('navbar.list'),
                    'route' => route('transport.index')
                ]
            ]
        ])
    @endauth
</ul>
