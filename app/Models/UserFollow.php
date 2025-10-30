<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class UserFollow extends Model
{
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    public function follower()
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    public function followee()
    {
        return $this->belongsTo(User::class, 'followee_id');
    }
}
