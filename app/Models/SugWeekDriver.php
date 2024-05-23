<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SugWeekDriver extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'sug-week-drivers';

    protected $fillable = [
        'booking-id',
        'driver-id',
        'passenger-id',
        'action',
        'viewed',
        'date-of-add',
        'date-of-edit'
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver-id');
    }

    public function passenger()
    {
        return $this->belongsTo(User::class, 'passenger-id');
    }

    public function driverinfo()
    {
        return $this->hasOne(DriverInfo::class, 'driver-id', 'driver-id');
    }
    public function booking()
    {
        return $this->belongsTo(WeekRideBooking::class, 'booking-id');
    }

    public function deliveryInfo()
    {
        return $this->hasOne(DelWeekInfo::class, 'sug-id', 'id');
    }

    public function rate()
    {
        return $this->hasOne(PassengerRate::class, 'user-id', 'passenger-id');
    }
}
