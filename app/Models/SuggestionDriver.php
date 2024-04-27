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

    public function scopeAccepted(Builder $query)
    {
        return $query->whereIn('action', [1, 2, 5]);
    }

    public function scopeRejected(Builder $query)
    {
        return $query->whereIn('action', [0, 3, 4]);
    }

    public function scopeNew(Builder $query)
    {
        return $query->where('action', 7);
    }
    public function scopeCancelled(Builder $query)
    {
        return $query->where('action', 7);
    }
    public function scopeDone(Builder $query)
    {
        return $query->where('action', 7);
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
        return $this->hasOne(DeliveryInfo::class, 'sug-id', 'id');
    }

    public function rate()
    {
        return $this->hasOne(PassengerRate::class, 'user-id', 'passenger-id');
    }
}
