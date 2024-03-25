<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoinsActions extends Model
{
    use HasFactory;
    protected $table = "coins_actions_mst";
    protected $primaryKey = "id";
    protected $fillable = [
        "name",
        "amount",
        "description",
        "status"
    ];
    protected $casts = [
        'status' => 'boolean'
    ];
}
