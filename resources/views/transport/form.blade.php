@extends('layouts.app')

@section('content')
    <transport-form
        :edit-data = "{{ isset($transport) ? json_encode($transport) : 'null' }}"
    ></transport-form>
@endsection
