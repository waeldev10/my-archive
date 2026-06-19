<?php

declare(strict_types=1);

namespace App\Models;

use Modules\Core\Traits\UsesUlid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Archive extends Model
{
    use UsesUlid, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'description',
        'content',
        'is_favorite',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_favorite' => 'boolean',
        ];
    }

    /**
     * Relationship: An archive belongs to a user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: An archive can have many tags (polymorphic).
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * Get the link extension model, if applicable.
     */
    public function link()
    {
        return $this->hasOne(ArchiveLink::class, 'id', 'id');
    }

    /**
     * Get the image extension model, if applicable.
     */
    public function image()
    {
        return $this->hasOne(ArchiveImage::class, 'id', 'id');
    }

    /**
     * Get the file extension model, if applicable.
     */
    public function file()
    {
        return $this->hasOne(ArchiveFile::class, 'id', 'id');
    }

    /**
     * Get the todo extension model, if applicable.
     */
    public function todo()
    {
        return $this->hasOne(ArchiveTodo::class, 'id', 'id');
    }

    /**
     * Get the plan extension model, if applicable.
     */
    public function plan()
    {
        return $this->hasOne(ArchivePlan::class, 'id', 'id');
    }

    /**
     * Get the project extension model, if applicable.
     */
    public function project()
    {
        return $this->hasOne(ArchiveProject::class, 'id', 'id');
    }

    /**
     * Get the course extension model, if applicable.
     */
    public function course()
    {
        return $this->hasOne(ArchiveCourse::class, 'id', 'id');
    }

    /**
     * Get the book extension model, if applicable.
     */
    public function book()
    {
        return $this->hasOne(ArchiveBook::class, 'id', 'id');
    }

    /**
     * Get the snippet extension model, if applicable.
     */
    public function snippet()
    {
        return $this->hasOne(ArchiveSnippet::class, 'id', 'id');
    }

    /**
     * Get the website extension model, if applicable.
     */
    public function website()
    {
        return $this->hasOne(ArchiveWebsite::class, 'id', 'id');
    }

    /**
     * Get the journal extension model, if applicable.
     */
    public function journal()
    {
        return $this->hasOne(ArchiveJournal::class, 'id', 'id');
    }

    /**
     * Get the extension model instance based on the archive type.
     */
    public function extension()
    {
        return match ($this->type) {
            'link' => $this->link(),
            'image' => $this->image(),
            'file' => $this->file(),
            'todo' => $this->todo(),
            'plan' => $this->plan(),
            'project' => $this->project(),
            'course' => $this->course(),
            'book' => $this->book(),
            'snippet' => $this->snippet(),
            'website' => $this->website(),
            'journal' => $this->journal(),
            default => null,
        };
    }
}
