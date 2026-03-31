<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FitnessVideo extends Model
{
    protected $table = 'fitness_videos';
    protected $fillable = [
            'title',
            'description',
            'video_url',
            'status',
            'created_by',
            'category_id',
            'tags',
            'img_url',
            'views'
        ];

        public function user()
    {        return $this->belongsTo(User::class, 'created_by', 'uid');
    }

    public function category()
    {        return $this->belongsTo(FitnessCategory::class, 'category_id', 'id');
    }

    public function likes()
    {
        return $this->hasMany(FitnessVideoLike::class, 'fitness_video_id', 'id');
    }
}
