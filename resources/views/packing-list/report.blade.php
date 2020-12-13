@extends('layouts.app')

@section('content')
    <packing-list-report
        :status-list = "{{ json_encode(\App\PackingList::statusAvailable()) }}"
    ></packing-list-report>
@endsection