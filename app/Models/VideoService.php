<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoService extends Model
{
    protected $fillable = [
        'url',
        'videoSize',
        'tournament_id',
    ];
  
    public function Tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function getAvatarUrlAttribute(): string
{
    return $this->avatar
        ? Storage::disk('s3')->url($this->avatar)
        : asset('images/default-avatar.png');
}

}