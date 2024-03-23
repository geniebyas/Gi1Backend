<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_uid',
        'reciever_uid',
        'title',
        'body',
        'img_url',
        'android_route',
        'is_read',
    ];

    // Define relationships
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_uid', 'uid');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_uid', 'uid');
    }
}
