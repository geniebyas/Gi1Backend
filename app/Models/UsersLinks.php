<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersLinks extends Model
{
    use HasFactory;

    protected $table="users_links";
    protected $primaryKey = "id";
    protected $fillable = [
        "uid",
        "link",
        "title",
        "clicks"
    ];

    public function user(){
        return $this->belongsTo(User::class,'uid');
    }

}
