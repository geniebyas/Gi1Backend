<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;
    
    protected $table = 'admin_mgt_tbl';

    protected $primaryKey = 'id';
    protected $fillable = [
        'username',
        'password',
        'email',
        'name',
    ];
}
