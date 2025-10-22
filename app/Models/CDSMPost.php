<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CDSMPost extends Model
{

    protected $table = 'cdsm_post';
    protected $fillable = [
        'img',
        'category',
        'caption',
        'location',
        'description',
        'tags',
        'views',
        'is_active',
        'uid'
    ];

    public function user(){
        return $this->belongsTo(User::class,"uid");
    }

    public function comments(){
        return $this->hasMany(CDSMPostComments::class);
    }

    public function likes(){
        return $this->hasMany(CDSMPostLikes::class);
    }

    public function interested(){
        return $this->hasMany(CDSMPostInterested::class);
    }


}
