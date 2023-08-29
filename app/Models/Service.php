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
        'cost',
        'date-of-add',
        'date-of-edit'
    ];

    use HasFactory;
}
