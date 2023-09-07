<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class University extends Model
{
    use HasFactory;

    protected $table = 'university';
    public $timestamps = false;

    protected $fillable = [
        'name-ar',
        'name-eng',
        'country',
        'city',
        'city_id',
        'location',
        'date-of-add',
        'date-of-edit'
    ];

    public function neighbours()
    {
        return $this->hasMany(Neighbour::class, 'city_id', 'id');
    }

    public function cityUni()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}
