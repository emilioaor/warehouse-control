@extends('layouts.app')

@section('content')
    <packing-list-form
        :edit-data = "{{ isset($packingList) ? json_encode($packingList) : 'null' }}"
        :transports = "{{ isset($transports) ? json_encode($transports) : '[]' }}"
    ></packing-list-form>
@endsection
