@extends('core::layouts.app', ['title' => __('Dashboard') . ' — ' . config('app.name')])

@section('content')
    @livewire('dashboard')
@endsection
