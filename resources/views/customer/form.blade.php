@extends('layouts.app')

@section('content')
    <customer-form
        :couriers = "{{ json_encode($couriers) }}"
        :edit-data = "{{ isset($customer) ? json_encode($customer) : 'null' }}"
    ></customer-form>
@endsection