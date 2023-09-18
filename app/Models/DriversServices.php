<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriversServices extends Model
{
    use HasFactory;

    protected $table = 'drivers-services';

    public $timestamps = false;

    protected $fillable = [
        'driver-id',
        'neighborhoods-to',
        'neighborhoods-from'
    ];
}
