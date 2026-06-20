@extends('core::layouts.app', ['title' => __('Forgot Password') . ' — ' . config('app.name')])

@section('content')
<div class="max-w-md mx-auto py-12">
    @livewire('auth.forgot-password')
</div>
@endsection
