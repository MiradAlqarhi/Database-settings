<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialMedia extends Model
{
    protected $table = 'social_media';

    protected $fillable = [
        'platform',
        'url',
        'user_id',
    ];
 
    protected function User(){
      return $this->hasOne(User::class);
 }
}