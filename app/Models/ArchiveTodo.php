<?php

declare(strict_types=1);

namespace App\Models;

use Modules\Core\Traits\UsesUlid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArchiveTodo extends Model
{
    use UsesUlid;

    /**
     * The table associated with the model.
     */
    protected $table = 'archive_todos';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'due_date',
        'completed_at',
        'priority',
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
            'due_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Relationship: The todo extension belongs to an archive.
     */
    public function archive(): BelongsTo
    {
        return $this->belongsTo(Archive::class);
    }
}
