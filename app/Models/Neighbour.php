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
        'neighborhood-eng',
        'city_id',
    ];

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}
