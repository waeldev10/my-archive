<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\UsesUlid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiMessage extends Model
{
    use UsesUlid;

    /**
     * The table associated with the model.
     */
    protected $table = 'ai_messages';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'conversation_id',
        'role',
        'content',
    ];

    /**
     * Indicates if the model should be timestamped.
     * Messages are immutable — only created_at is used.
     */
    public $timestamps = false;

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (AiMessage $message): void {
            $message->created_at = $message->created_at ?? now();
        });
    }

    /**
     * Relationship: A message belongs to a conversation.
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(AiConversation::class);
    }
}
