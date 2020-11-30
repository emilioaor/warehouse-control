@extends('layouts.app')

@section('content')
    <order-form
        :edit-data = "{{ isset($order) ? json_encode($order) : 'null' }}"
    ></order-form>
@endsection