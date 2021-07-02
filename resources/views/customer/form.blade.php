@extends('layouts.app')

@section('content')
    <customer-form
        :edit-data = "{{ isset($customer) ? json_encode($customer) : 'null' }}"
        :sectors = "{{ json_encode($sectors) }}"
    ></customer-form>
@endsection
