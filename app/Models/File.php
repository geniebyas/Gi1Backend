<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;
    protected $tableName = 'files_mgt_tbl';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'extension',
        'path',
        'size',
        'type'
    ];
}
