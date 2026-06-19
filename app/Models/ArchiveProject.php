<?php

declare(strict_types=1);

namespace App\Models;

use Modules\Core\Traits\UsesUlid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArchiveProject extends Model
{
    use UsesUlid;

    /**
     * The table associated with the model.
     */
    protected $table = 'archive_projects';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'start_date',
        'end_date',
        'status',
        'repository_url',
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
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * Relationship: The project extension belongs to an archive.
     */
    public function archive(): BelongsTo
    {
        return $this->belongsTo(Archive::class);
    }
}
