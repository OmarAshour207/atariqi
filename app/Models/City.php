<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Neighbour;
class City extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'city-ar',
        'city-en'
    ];

    public function neighbours()
    {
        return $this->hasMany(Neighbour::class, 'city_id');
    }
}
