@extends('layouts.app')

@section('content')
    <order-report
        :status-list = "{{ json_encode(\App\Order::statusAvailable()) }}"
    ></order-report>
@endsection