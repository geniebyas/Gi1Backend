<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoLike extends Model
{
    protected $fillable = [
        'video_id',
        'uid',
        'is_liked',
        'created_at',
        'updated_at',
    ];

    public function video()
    {
        return $this->belongsTo(Video::class, 'video_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'uid');
    }
}
