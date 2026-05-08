<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'age',
        'gender',
        'game',
        'user_id'
    ];
    public function User()
{
    return $this->belongsTo(User::class);
}
   public function medalCount()
    {
        return $this->hasMany(MedalCount::class);
    }

    public function tournaments()
    {
        return $this->hasMany(Tournament::class);
    }

}