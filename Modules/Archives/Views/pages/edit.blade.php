@extends('core::layouts.app', ['title' => __('Edit :type', ['type' => $typeLabel]) . ' — ' . config('app.name')])

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    @livewire('archives.edit', [
        'type' => $type,
        'archiveId' => $archiveId,
    ], key('edit-'.$archiveId))
</div>
@endsection
