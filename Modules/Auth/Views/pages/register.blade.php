@extends('core::layouts.app', ['title' => __('Register') . ' — ' . config('app.name')])

@section('content')
<div class="max-w-md mx-auto py-12">
    @livewire('auth.register')
</div>
@endsection
