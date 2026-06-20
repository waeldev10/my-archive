@extends('core::layouts.app', ['title' => __('Log In') . ' — ' . config('app.name')])

@section('content')
<div class="max-w-md mx-auto py-12">
    @livewire('auth.login')
</div>
@endsection
