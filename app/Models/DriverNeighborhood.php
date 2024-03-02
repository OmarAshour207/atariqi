<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverNeighborhood extends Model
{
    public $table = 'drivers-neighborhoods';

    public $timestamps = false;

    protected $fillable = [
        'driver-id',
        'neighborhoods-to',
        'neighborhoods-from'
    ];

}
