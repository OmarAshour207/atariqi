<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DayRideBooking extends Model
{
    use HasFactory;

    protected $table = 'day-ride-booking';

    public $timestamps = false;

    protected $fillable = [
        'passenger-id',
        'neighborhood-id',
        'university-id',
        'service-id',
        'date-of-ser',
        'road-way',
        'time-go',
        'time-back',
        'action',
        'date-of-add',
        'lat',
        'lng'
    ];

    // Scopes

    public function scopeFinishedTrips(Builder $query, $userId, ...$dates)
    {
        return $query->whereBetween('date-of-ser', $dates)
            ->whereHas('sugDriver', function ($query) use ($userId) {
                $query->where('action', 6)->where('driver-id', $userId);
            });
    }

    // relations
    public function passenger()
    {
        return $this->belongsTo(User::class, 'passenger-id');
    }

    public function neighborhood()
    {
        return $this->belongsTo(Neighbour::class, 'neighborhood-id');
    }
    public function university()
    {
        return $this->belongsTo(University::class, 'university-id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service-id');
    }

    public function sugDriver()
    {
        return $this->hasOne(SugDayDriver::class, 'booking-id', 'id');
    }

}
