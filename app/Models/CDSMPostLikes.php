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
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function post(){
        return $this->belongsTo(CDSMPost::class);
    }
}
