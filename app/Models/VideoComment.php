<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoComment extends Model
{
    protected $table = 'video_comments';
    protected $fillable = [
        'video_id',
        'uid',
        'is_active',
        'comment',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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
