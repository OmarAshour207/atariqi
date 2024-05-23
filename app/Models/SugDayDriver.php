<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SugDayDriver extends Model
{
    use HasFactory;

    protected $table = 'sug-day-drivers';

    public $timestamps = false;

    protected $fillable = [
        'booking-id',
        'driver-id',
        'passenger-id',
        'action',
        'date-of-add',
        'date-of-edit',
        'viewed'
    ];

    // Scopes

    public function scopeAction(Builder $query, $value): Builder
    {
        return $query->where('action', $value);
    }
    public function scopeDate(Builder $query, $date)
    {
        return $query->whereDate('date-of-add', $date);
    }

    // relations
    public function passenger()
    {
        return $this->belongsTo(User::class, 'passenger-id');
    }
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver-id');
    }

    public function driverinfo()
    {
        return $this->hasOne(DriverInfo::class, 'driver-id', 'driver-id');
    }
    public function booking(): BelongsTo
    {
        return $this->belongsTo(DayRideBooking::class, 'booking-id');
    }

    public function deliveryInfo()
    {
        return $this->hasOne(DelDailyInfo::class, 'sug-id', 'id');
    }

    public function rate()
    {
        return $this->hasOne(PassengerRate::class, 'user-id', 'passenger-id');
    }
}
