<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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

    // Scopes

    public function scopeAction(Builder $query, ...$action): Builder
    {
        return $query->whereIn('action', $action);
    }
    public function scopeDate(Builder $query, $date): Builder
    {
        return $query->whereDate('date-of-add', $date);
    }

    public function scopeFinishedTrips(Builder $query, $userId, ...$dates)
    {
        return $query->where('action', 5)
            ->whereDate('date-of-add', '>=', $dates[0])
            ->whereDate('date-of-add', '<=', $dates[1])
//            ->whereBetween('date-of-add', $dates)
            ->where('driver-id', $userId);
    }

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

    public function booking()
    {
        return $this->belongsTo(RideBooking::class, 'booking-id');
    }

    public function deliveryInfo()
    {
        return $this->hasOne(DelImmediateInfo::class, 'sug-id', 'id');
    }

    public function rate()
    {
        return $this->hasOne(PassengerRate::class, 'user-id', 'passenger-id');
    }
}
