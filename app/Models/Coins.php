<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coins extends Model
{
    use HasFactory;

    protected $table = 'coins_mst';
    protected $primaryKey = 'id';
    protected $fillable = [
        "uid",
        "type",
        "action_id",
        "amount",
        "description"
    ];

    protected $casts = [
        "action_id"=>'integer'
    ];


    public function user(){
        return $this->belongsTo(User::class, 'uid');
    }

    public function action(){
        return $this->hasOne(CoinsActions::class, 'id', 'action_id');
    }

}
