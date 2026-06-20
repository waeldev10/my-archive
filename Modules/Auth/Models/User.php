<?php

declare(strict_types=1);

namespace Modules\Auth\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Modules\Auth\Notifications\VerifyEmailNotification;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Core\Enums\UserRole;
use Modules\Core\Traits\UsesUlid;

#[UseFactory(UserFactory::class)]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, UsesUlid;
    use \Illuminate\Auth\MustVerifyEmail {
        sendEmailVerificationNotification as protected traitSendEmailVerification;
    }

    /**
     * Send the email verification notification,
     * using the custom VerifyEmailNotification class.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification());
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    #[Hidden(['password', 'remember_token'])]
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    /**
     * Check if the user has an admin role.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /*
     * Relationship: A user has many archives.
     */
    public function archives(): HasMany
    {
        return $this->hasMany(\Modules\Archives\Models\Archive::class);
    }

    /**
     * Relationship: A user has many tags.
     */
    public function tags(): HasMany
    {
        return $this->hasMany(\Modules\Tags\Models\Tag::class);
    }

    /**
     * Relationship: A user has one preferences record.
     */
    public function preferences(): HasOne
    {
        return $this->hasOne(UserPreference::class);
    }

    /**
     * Relationship: A user has one telegram connection.
     */
    public function telegramConnection(): HasOne
    {
        return $this->hasOne(\Modules\Telegram\Models\TelegramConnection::class);
    }

    /**
     * Relationship: A user has many AI conversations.
     */
    public function aiConversations(): HasMany
    {
        return $this->hasMany(\Modules\Ai\Models\AiConversation::class);
    }

    /**
     * Relationship: A user has many activity logs.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(\Modules\Dashboard\Models\ActivityLog::class);
    }


}
