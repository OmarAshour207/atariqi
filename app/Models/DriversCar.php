<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriversCar extends Model
{
    use HasFactory;

    public $table = 'drivers-car';

    public $timestamps = false;

    protected $fillable = [
        'driver-id',
        'driver-type-id',
        'car_form_img',
        'license_img',
        'car_front_img',
        'car_back_img',
        'car_rside_img',
        'car_lside_img',
        'car_insideFront_img',
        'car_insideBack_img',
        'date_of_add',
        'date_of_edit'
    ];

    // relations
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver-id');
    }

    public function driverType(): BelongsTo
    {
        return $this->belongsTo(DriverType::class, 'driver-type-id');
    }

}
