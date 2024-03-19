<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndustryDiscussionLike extends Model
{
    protected $fillable = ['uid', 'discussion_id'];
    protected $table = "discussion_likes";

    public function discussion()
    {
        return $this->belongsTo(IndustryDiscussion::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'uid');
    }
}
