<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverInfo extends Model
{
    use HasFactory;

    protected $table = 'driver-info';

    public $timestamps = false;

    protected $fillable = [
        'driver-id',
        'car-brand',
        'car-model',
        'car-number',
        'car-letters',
        'car-color',
        'driver-neighborhood',
        'driver-rate',
        'driver-license-link',
        'allow-disabilities',
        'approval',
        'date-of-add',
        'date-of-edit'
    ];

    protected $casts = [
        'driver-rate'  => 'double'
    ];

    // relations
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver-id');
    }

    public function schedule()
    {
        return $this->hasOne(DriverSchedule::class, 'driver-id', 'driver-id');
    }
}
