<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverType extends Model
{
    use HasFactory;

    public $table = 'driver_type';

    public $timestamps = false;

    protected $fillable = [
        'name-ar',
        'name-eng'
    ];
}
