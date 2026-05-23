<?php

namespace App\Models;

use App\Models\SocialMedia;
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
    return $this->hasOne(MedalCount::class);
}

    public function tournaments()
    {
        return $this->hasMany(Tournament::class);
    }

    public function socialMedia()
{
    return $this->hasMany(SocialMedia::class, 'user_id', 'user_id');
}

public function getWinsAttribute()
    {
        return $this->medalCount->gold ?? 0;
    }

    public function getWinRateAttribute()
    {
        $wins = $this->wins;
        $total = $this->tournaments_count ?? $this->tournaments()->count();

        return $total > 0 ? round(($wins / $total) * 100, 2) : 0;
    }

}