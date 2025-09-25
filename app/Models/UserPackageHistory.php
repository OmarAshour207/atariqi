<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPackageHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'package_id',
        'status',
        'start_date',
        'end_date',
        'canceled_date',
        'interval',
    ];


}
