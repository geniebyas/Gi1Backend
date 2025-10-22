<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CDSMPostLikes extends Model
{
    protected $table = 'cdsm_post_likes';
    protected $fillable = [
        'post_id',
        'uid',
        'is_liked'
    ];

    protected $casts = [
        'is_liked'=>"boolean"
    ];
    public function user(){
        return $this->belongsTo(User::class,"uid");
    }

    public function post(){
        return $this->belongsTo(CDSMPost::class,"post_id");
    }
}
