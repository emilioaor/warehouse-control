@extends('layouts.app')

@section('content')
    <packing-list-form
        :edit-data = "{{ isset($packingList) ? json_encode($packingList) : 'null' }}"
    ></packing-list-form>
@endsection