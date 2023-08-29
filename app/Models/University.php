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
        'location',
        'date-of-add',
        'date-of-edit'
    ];
}
