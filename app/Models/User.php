<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'image_path'
    ];

    protected $appends = ['imagePathUrl', 'is_followed'];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
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
        ];
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function likes()
    {
        return $this->hasMany(PostLike::class);
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class);
    }

    public function followers()
    {
        return $this->hasMany(UserFollow::class, 'followee_id');
    }

    public function followings()
    {
        return $this->hasMany(UserFollow::class, 'follower_id');
    }

    public function getImagePathUrlAttribute(): ?string
    {
        if (! $this->image_path) {
            return asset('assets/img/avatar.jpg');
        }

        return Storage::disk('public')->url($this->image_path);
    }

    public function getIsFollowedAttribute(): bool
    {
        $authUser = auth()->user();

        if (! $authUser) {
            return false;
        }

        return $this->followers()
            ->where('follower_id', $authUser->id)
            ->exists();
    }
}
