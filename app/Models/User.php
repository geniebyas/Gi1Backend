<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $primaryKey = 'uid';
    protected $fillable = [
        'name',
        'username',
        'uid',
        'email',
        'profile_pic',
        'password',
        'phone',
        'dob',
        'gender',
        'city',
        'bio'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function responses(){
        return $this->hasMany(FeedbackUsersResponse::class,'uid');
    }

    public function wallet(){
        return $this->hasOne("user_wallet",'uid','uid');
    }

    public function settings(){
        return $this->hasOne(UsersSetting::class,'uid','uid');
    }

    public function transactions(){
        return $this->hasMany(Coins::class,'uid');
    }

    /**
     * Get the connections where the user is the source user.
     */
    public function connections(){
        return $this->hasMany(UsersConnection::class,'source_uid','uid')->where('status','accepted');
    }

    /**
     * Get the connectors (followers) of the user.
     */
    public function connectors()
    {
        return $this->hasMany(UsersConnection::class, 'dest_uid', 'uid')->where('status', 'accepted');
    }


    public function hasSentFriendRequest($uid)
    {
        return UsersConnection::where('source_uid', $this->uid)
            ->where('dest_uid', $uid)
            ->where('status', 'pending')
            ->exists();
    }
      /**
     * Check if the user has a pending friend request from the given user.
     */
    public function hasPendingFriendRequest($dest_uid)
    {
        return UsersConnection::where('source_uid', $dest_uid)
            ->where('dest_uid', $this->uid)
            ->where('status', 'pending')
            ->exists();
    }

    /**
     * Check if the user is friends with the given user.
     */
    public function isFriendWith($uid)
    {
        return UsersConnection::where(function ($query) use ($uid) {
                $query->where('source_uid', $this->uid)
                    ->where('dest_uid', $uid)
                    ->where('status', 'accepted');
            })
            ->orWhere(function ($query) use ($uid) {
                $query->where('source_uid', $uid)
                    ->where('dest_uid', $this->uid)
                    ->where('status', 'accepted');
            })
            ->exists();
    }



}
