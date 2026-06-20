<?php

declare(strict_types=1);

namespace Modules\Archives\Http\Controllers\Web;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;
use Modules\Archives\Services\ArchiveFactory;

class ArchiveController extends Controller
{
    /**
     * Display a listing of archives for a given type.
     */
    public function index(string $type): View
    {
        $typeLabel = $this->resolveTypeLabel($type);

        return view('archives::pages.index', [
            'type' => $type,
            'typeLabel' => $typeLabel,
        ]);
    }

    /**
     * Show the form for creating a new archive.
     */
    public function create(string $type): View
    {
        $typeLabel = $this->resolveTypeLabel($type);

        return view('archives::pages.create', [
            'type' => $type,
            'typeLabel' => $typeLabel,
        ]);
    }

    /**
     * Display the specified archive.
     */
    public function show(string $type, string $archive): View
    {
        $typeLabel = $this->resolveTypeLabel($type);

        return view('archives::pages.show', [
            'type' => $type,
            'typeLabel' => $typeLabel,
            'archiveId' => $archive,
        ]);
    }

    /**
     * Show the form for editing the specified archive.
     */
    public function edit(string $type, string $archive): View
    {
        $typeLabel = $this->resolveTypeLabel($type);

        return view('archives::pages.edit', [
            'type' => $type,
            'typeLabel' => $typeLabel,
            'archiveId' => $archive,
        ]);
    }

    /**
     * Resolve a human-readable label for the archive type.
     */
    private function resolveTypeLabel(string $type): string
    {
        return ucfirst($type);
    }
}
