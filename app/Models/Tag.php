<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\UsesUlid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    use UsesUlid;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'color',
    ];

    /**
     * Relationship: A tag belongs to a user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: A tag can be attached to many archives (polymorphic).
     */
    public function archives(): MorphToMany
    {
        return $this->morphedByMany(Archive::class, 'taggable');
    }
}
