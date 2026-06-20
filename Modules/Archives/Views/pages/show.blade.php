@extends('core::layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    @livewire('archives.show', [
        'type' => $type,
        'archiveId' => $archiveId,
    ], key('show-'.$archiveId))
</div>
@endsection
