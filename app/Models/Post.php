<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected $appends = ['imagePathUrl', 'is_liked', 'is_commented'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->hasMany(PostLike::class);
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class);
    }

    public function getImagePathUrlAttribute(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        return Storage::disk('public')->url($this->image_path);
    }

    public function getIsLikedAttribute(): bool
    {
        $user = auth()->user();
        if (! $user) return false;

        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function getIsCommentedAttribute(): bool
    {
        $user = auth()->user();
        if (! $user) return false;

        return $this->comments()->where('user_id', $user->id)->exists();
    }
}
