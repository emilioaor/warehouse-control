@extends('layouts.app')

@section('content')
    <customer-form
        :edit-data = "{{ isset($customer) ? json_encode($customer) : 'null' }}"
    ></customer-form>
@endsection