<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'is_announcement',
        'views',
        'topic',
        'img_url',
        'android_route',
    ];

    protected $casts =[
        'is_announcement'=>'boolean',
        'views'=>'integer'
    ];





}
