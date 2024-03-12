<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWallet extends Model
{
    use HasFactory;
    protected $table="user_wallet";
    protected $primaryKey = "id";
    protected $fillable = [
        "uid",
        "total_bal"
    ];

    
    public function user(){
        return $this->belongsTo(User::class, 'id');
    }
}
