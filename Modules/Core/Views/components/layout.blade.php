@props([
    'title' => null,
])

@extends('core::layouts.app')

@section('styles')
    @stack('styles')
@endsection

@section('scripts')
    @stack('scripts')
@endsection

{{-- The app layout renders $slot; this component just wraps the content --}}
{{ $slot }}
