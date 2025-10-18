<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CDSMPostComments extends Model
{
    protected $table = 'cdsm_post_comments';
    protected $fillable = [
        'post_id',
        'uid',
        'comment',
        'likes'
    ];
    public function user(){
        return $this->belongsTo('User');
    }
    public function post(){
        return $this->belongsTo('CDSMPost');
    }

}
