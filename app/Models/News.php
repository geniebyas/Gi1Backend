<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $table = 'news';

    protected $fillable = [
        'title',
        'content',
        'uid',
        'img_url',
        'is_active',
        'is_featured',
        'category',
        'likes',
        'slug',
        'tags',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'uid');
    }

    public function analytics()
    {
        return $this->hasMany(NewsAnalytics::class, 'news_id');
    }

}
