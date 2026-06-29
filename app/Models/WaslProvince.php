<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaslProvince extends Model
{
    protected $fillable = [
        'province_id',
        'province_name',
        'region_name',
        'province_name_normalized',
        'synced_at',
    ];

    protected $casts = [
        'province_id' => 'integer',
        'synced_at' => 'datetime',
    ];
}
