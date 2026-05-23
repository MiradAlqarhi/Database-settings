<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class scout extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'organization_name',
        'workEmail',
        'users_id'
    ];

    public function User()
{
    return $this->belongsTo(User::class);
}

 public function socialMedia()
{
    return $this->hasMany(SocialMedia::class, 'user_id', 'user_id');
}
}