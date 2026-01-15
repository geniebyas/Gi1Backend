<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoSave extends Model
{
    protected $table = 'video_saves';
    protected $fillable = [
        'video_id',
        'uid',
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
