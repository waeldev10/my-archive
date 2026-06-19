<?php

declare(strict_types=1);

namespace App\Models;

use Modules\Core\Traits\UsesUlid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArchivePlan extends Model
{
    use UsesUlid;

    /**
     * The table associated with the model.
     */
    protected $table = 'archive_plans';

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
        'progress',
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
     * Relationship: The plan extension belongs to an archive.
     */
    public function archive(): BelongsTo
    {
        return $this->belongsTo(Archive::class);
    }
}
