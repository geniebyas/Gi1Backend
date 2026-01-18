<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $table = 'news';

    protected $fillable = [
        'title',
        'uid',
        'img_url',
        'is_active',
        'is_featured',
        'category',
        'likes',
        'slug',
        'tags',
        'meta_title',
    'meta_description',
    'meta_keywords',
    'content_json', // Store EditorJS JSON
    'content_html', // Rendered HTML for quick display
    'reading_time', // Calculated reading time in minutes
    'shares_count', // Track social shares
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
