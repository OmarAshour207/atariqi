<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'services';

    public $timestamps = false;
    protected $fillable = [
        'service',
        'service-ar',
        'service-eng',
        'cost',
        'road-way',
        'date-of-add',
        'date-of-edit'
    ];

    use HasFactory;
}
