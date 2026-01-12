<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    use HasFactory;

    protected $table = 'industries';

    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'description',
        'thumbnail',
        'file',
        'type',
        'ispinned',
        'status',
        'is_discussion_allowed',
        'pinnedthumb',
        'path'
    ];

    protected $casts = [
        'ispinned' => 'boolean',
        'status' => 'boolean',
        'is_discussion_allowed' => 'boolean'
    ];

    public function views(){
        return $this->hasMany(IndustryView::class,'industry_id','id');
    }

    public function discussions(){
        return $this->hasMany(IndustryDiscussion::class,'industry_id','id');
    }

}
