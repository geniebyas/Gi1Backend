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
        "amount"
    ];

    protected $casts = [
        "amount"=>'decimal'
    ];


}
