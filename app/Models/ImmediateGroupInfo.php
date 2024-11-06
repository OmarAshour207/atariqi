<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImmediateGroupInfo extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'immediate-group-info';

    protected $fillable = [
        'ride-id',
        'group-id',
        'date-of-add'
    ];
}
