<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndustryReply extends Model
{
    protected $fillable = ['industry_id','discussion_id', 'uid', 'msg'];

    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'uid');
    }

    public function likes()
    {
        return $this->hasMany(IndustryReplyLike::class,'reply_id','id');
    }
}
