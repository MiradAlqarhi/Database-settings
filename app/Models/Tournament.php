<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    protected $fillable = [
        'certificateType',
        'tournamentName',
        'tournamentdate'
    ];
}