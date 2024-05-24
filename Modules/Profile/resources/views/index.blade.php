@extends('profile::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('profile.name') !!}</p>
@endsection
