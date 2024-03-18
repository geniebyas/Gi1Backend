<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersSetting extends Model
{
    use HasFactory;

    protected $table = "users_settings";
    protected $primaryKey = "id";
    protected $fillable = [
        'uid',
        'is_private',
        'refer_code',
        'referred_by'
    ];

    protected $casts = [
        'is_private' => 'boolean',
    ];
    
    public function user(){
        return $this->belongsTo(User::class, 'uid');
    }

    public function referrer(){
        return $this->belongsTo(User::class,'referred_by','uid');
    }

}
