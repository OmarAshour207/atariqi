<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Neighbour extends Model
{
    use HasFactory;

    protected $table = 'neighborhoods';
    public $timestamps = false;

    protected $fillable = [
        'neighborhood-ar',
        'neighborhood-en',
        'city-id'
    ];

}
