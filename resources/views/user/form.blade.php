@extends('layouts.app')

@section('content')
    <user-form
        :roles = "{{ json_encode(\App\User::rolesAvailable()) }}"
        :edit-data = "{{ isset($user) ? json_encode($user) : 'null' }}"
    ></user-form>
@endsection