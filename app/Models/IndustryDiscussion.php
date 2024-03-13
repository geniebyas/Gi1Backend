<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndustryDiscussion extends Model
{
    use HasFactory;

    protected $fillable = ['industry_id', 'uid', 'msg'];

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
        return $this->hasMany(IndustryDiscussionLike::class);
    }


}