<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedalCount extends Model
{
    protected $fillable = [
        'player_id',
        'gold',
        'silver',
        'bronze',
    ];
  
  public function increaseMedal($medalType)
{
    if (in_array($medalType, ['gold', 'silver', 'bronze'])) {
        $this->increment($medalType);
    }
}

 public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
