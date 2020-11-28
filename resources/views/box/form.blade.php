@extends('layouts.app')

@section('content')
    <box-form
        :edit-data = "{{ isset($box) ? json_encode($box) : 'null' }}"
    ></box-form>
@endsection