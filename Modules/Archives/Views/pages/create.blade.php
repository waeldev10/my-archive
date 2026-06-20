@extends('core::layouts.app', ['title' => __('New :type', ['type' => $typeLabel]) . ' — ' . config('app.name')])

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <x-core::page-header
        :title="__('New :type', ['type' => $typeLabel])"
        :description="__('Create a new archive entry')"
        :back-route="route('archives.list', ['type' => $type])"
        :back-label="__('Back to :type', ['type' => $typeLabel . 's'])"
    />

    @livewire('archives.create', ['type' => $type], key('create-'.$type))
</div>
@endsection
