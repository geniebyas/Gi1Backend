<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndustryReplyLike extends Model
{
    protected $fillable = ['uid', 'reply_id'];

    public function reply()
    {
        return $this->belongsTo(IndustryReply::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'uid');
    }
}
