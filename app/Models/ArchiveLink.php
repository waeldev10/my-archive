<?php

declare(strict_types=1);

namespace App\Models;

use Modules\Core\Traits\UsesUlid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArchiveLink extends Model
{
    use UsesUlid;

    /**
     * The table associated with the model.
     */
    protected $table = 'archive_links';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'url',
        'domain',
        'preview_image',
        'preview_description',
    ];

    /**
     * Indicates if the ID is auto-incrementing.
     */
    public $incrementing = false;

    /**
     * Relationship: The link extension belongs to an archive.
     */
    public function archive(): BelongsTo
    {
        return $this->belongsTo(Archive::class);
    }
}
