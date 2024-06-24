<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverAnnounce extends Model
{
    use HasFactory;

    protected $table = 'driver-announce';

    public $timestamps = false;

    protected $fillable = [
        'title-ar',
        'title-eng',
        'content-ar',
        'content-eng'
    ];
}
