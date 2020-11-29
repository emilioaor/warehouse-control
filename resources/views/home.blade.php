@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body bg-success text-white">
                    <h5><strong>{{ __('dashboard.ordersToday') }}</strong></h5>
                    <div class="d-flex">
                        <i class="fa fa-list-alt" style="font-size: 80px"></i>

                        <div class="px-3 w-100 text-center" style="font-size: 40px">{{ $orders }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body bg-secondary text-white">
                    <h5><strong>{{ __('dashboard.customersToday') }}</strong></h5>
                    <div class="d-flex">
                        <i class="fa fa-address-book" style="font-size: 80px"></i>

                        <div class="px-3 w-100 text-center" style="font-size: 40px">{{ $customers }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body bg-info text-white">
                    <h5><strong>{{ __('dashboard.boxesToday') }}</strong></h5>
                    <div class="d-flex">
                        <i class="fa fa-box" style="font-size: 80px"></i>

                        <div class="px-3 w-100 text-center" style="font-size: 40px">{{ $boxes }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
