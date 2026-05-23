<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; 

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; 

    protected $fillable = [
        'email',
        'password',
        'type',
        'profile_completed',
        'avatar'
    ];

    protected $hidden = [
        'password',
    ];

    public function player()
    {
        return $this->hasOne(Player::class);
    }

    public function scout()
    {
        return $this->hasOne(scout::class);
    }

    protected function casts(): array
    {
        return [
            'profile_completed' => 'boolean',
            'password' => 'hashed',
        ];
    }

 protected function SocialMedia(){
      return $this->hasOne(SocialMedia::class);
 }

 public function following()
{
    return $this->belongsToMany(
        User::class,
        'follows',
        'follower_id',
        'following_id'
    );
}

public function followers()
{
    return $this->belongsToMany(
        User::class,
        'follows',
        'following_id',
        'follower_id'
    );
}
}