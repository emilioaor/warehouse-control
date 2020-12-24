@extends('layouts.app')

@section('content')
    <order-form
        :edit-data = "{{ isset($order) ? json_encode($order) : 'null' }}"
        :ways-available = "{{ json_encode(\App\Order::waysAvailable()) }}"
    ></order-form>
@endsection
