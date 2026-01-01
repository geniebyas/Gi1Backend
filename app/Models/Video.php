<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'title',
        'url',
        'thumbnail',
        'description',
        'is_featured',
        'is_active',
        'uid',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
    ];
    public function comments()
    {
        return $this->hasMany(VideoComment::class);
    }
    public function saves()
    {
        return $this->hasMany(VideoSave::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'uid');
    }
    public function likes()
    {
        return $this->hasMany(VideoLike::class);
    }
}
