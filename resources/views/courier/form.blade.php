@extends('layouts.app')

@section('content')
    <courier-form
        :edit-data = "{{ isset($courier) ? json_encode($courier) : 'null' }}"
        :sectors = "{{ json_encode($sectors) }}"
    ></courier-form>
@endsection
