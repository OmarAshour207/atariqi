<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuggestionDriver extends Model
{
    use HasFactory;

    protected $table = 'suggestions-drivers';

    public $timestamps = false;

    protected $fillable = [
        'booking-id',
        'driver-id',
        'passenger-id',
        'action',
        'date-of-add',
        'date-of-edit'
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver-id');
    }

    public function driverinfo()
    {
        return $this->hasOne(DriverInfo::class, 'driver-id', 'driver-id');
    }

    public function booking()
    {
        return $this->belongsTo(RideBooking::class, 'booking-id');
    }
}
