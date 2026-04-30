<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // ✅

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // ✅

    protected $fillable = [
        'email',
        'password',
        'type',
        'profile_completed',
    ];

    protected $hidden = [
        'password',
    ];

    public function player()
    {
        return $this->hasOne(Player::class);
    }

    protected function casts(): array
    {
        return [
            'profile_completed' => 'boolean',
            'password' => 'hashed',
        ];
    }
}