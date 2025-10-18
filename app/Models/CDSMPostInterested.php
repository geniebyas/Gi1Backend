<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CDSMPostInterested extends Model
{

    protected $table = 'cdsm_post_interested';
    protected $fillable = [
        'post_id',
        'uid'
    ];
    public function user(){
        return $this->belongsTo('User');
    }
    public function post(){
        return $this->belongsTo('CDSMPost');
    }
}
