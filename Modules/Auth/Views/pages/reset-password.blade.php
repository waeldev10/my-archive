@extends('core::layouts.app', ['title' => __('Reset Password') . ' — ' . config('app.name')])

@section('content')
<div class="max-w-md mx-auto py-12">
    @livewire('auth.reset-password', ['token' => $token])
</div>
@endsection
