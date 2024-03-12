<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersConnection extends Model
{
    use HasFactory;
    protected $table = 'users_connection';

    protected $fillable = ['source_uid', 'dest_uid', 'status'];

    /**
     * Get the source user.
     */
    public function sourceUser()
    {
        return $this->belongsTo(User::class, 'source_uid', 'uid');
    }

    /**
     * Get the destination user.
     */
    public function destUser()
    {
        return $this->belongsTo(User::class, 'dest_uid', 'uid');
    }
}
