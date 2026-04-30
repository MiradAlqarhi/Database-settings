<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    // These are the table columns that can be filled
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
}