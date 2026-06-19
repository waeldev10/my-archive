<?php

declare(strict_types=1);

namespace App\Models;

use Modules\Core\Traits\UsesUlid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArchiveJournal extends Model
{
    use UsesUlid;

    /**
     * The table associated with the model.
     */
    protected $table = 'archive_journals';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'entry_date',
        'mood',
        'location',
    ];

    /**
     * Indicates if the ID is auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
        ];
    }

    /**
     * Relationship: The journal extension belongs to an archive.
     */
    public function archive(): BelongsTo
    {
        return $this->belongsTo(Archive::class);
    }
}
