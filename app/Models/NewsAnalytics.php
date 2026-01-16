<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsAnalytics extends Model
{
    protected $table = 'news_analytics';

    protected $fillable = [
        'news_id',
        'ip_address',
        'user_agent',
        'country',
        'city',
        'region',
        'latitude',
        'longitude',
        'device_type',
        'browser',
        'operating_system',
        'session_id',
        'is_unique',
        'utm_source',
        'utm_medium',
        'utm_campaign',
    ];

    public function news()
    {
        return $this->belongsTo(News::class, 'news_id');
    }

}
