<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FitnessVideoLike extends Model
{
    protected $table = 'fitness_videos_likes';
    protected $fillable = [
            'video_id',
            'user_id',
            'is_liked',
        ];

        public function video()
    {        return $this->belongsTo(FitnessVideo::class, 'video_id', 'id');
    }

    public function user()
    {        return $this->belongsTo(User::class, 'user_id', 'uid');
    }
}
