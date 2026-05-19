<?php

namespace App\Models;
use App\Models\SocialMedia;//اضفت ذا
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
        'contact_email',
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
    //اضفت ذا
public function socialMedia()
{
    return $this->hasMany(SocialMedia::class, 'user_id', 'user_id');
}
}