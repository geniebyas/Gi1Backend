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
        'img_url',
        'android_route',
    ];
}
