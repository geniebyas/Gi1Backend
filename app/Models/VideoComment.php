<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoComment extends Model
{
    protected $fillable = [
        'video_id',
        'uid',
        'comment',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
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
