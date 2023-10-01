<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SugDayDriver extends Model
{
    use HasFactory;

    protected $table = 'sug-day-driver';

    public $timestamps = false;

    protected $fillable = [
        'booking-id',
        'driver-id',
        'action',
        'date-of-add',
        'date-of-edit'
    ];

    // relations
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver-id');
    }

    public function booking()
    {
        return $this->belongsTo(DayRideBooking::class, 'booking-id');
    }
}
