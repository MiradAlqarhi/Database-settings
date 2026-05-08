<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    protected $fillable = [
        'certificateType',
        'tournamentName',
        'tournamentdate',
        'rank',
        'player_id'
    ];


    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}