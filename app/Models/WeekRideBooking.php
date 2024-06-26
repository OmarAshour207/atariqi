<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class WeekRideBooking extends Model
{
    use HasFactory;

    protected $table = 'week-ride-booking';

    public $timestamps = false;

    protected $casts = [
        'group-id' => 'string'
    ];

    protected $fillable = [
        'neighborhood-id',
        'passenger-id',
        'university-id',
        'service-id',
        'group-id',
        'date-of-ser',
        'road-way',
        'time-go',
        'time-back',
        'lat',
        'lng',
        'action',
        'status'
    ];

    // Scopes
    public function scopeDate(Builder $query, $date): Builder
    {
        return $query->whereDate('date-of-ser', $date);
    }

    public function scopeAction(Builder $query, $value): Builder
    {
        return $query->where('action', $value);
    }

    public function scopeStatus(Builder $query, $value): Builder
    {
        return $query->where('status', $value);
    }

    public function scopeFinishedTrips(Builder $query, $userId, ...$dates): Builder
    {
        return $query
            ->whereDate('date-of-ser', '>=', $dates[0]['start_date'])
            ->whereDate('date-of-ser', '<=', $dates[0]['end_date'])
//            ->whereBetween('date-of-ser', $dates)
            ->whereHas('sugDriver', function ($query) use ($userId) {
                $query->where('action', 6)->where('driver-id', $userId);
            });
    }

    // relations

    public function neighborhood()
    {
        return $this->belongsTo(Neighbour::class, 'neighborhood-id');
    }

    public function university()
    {
        return $this->belongsTo(University::class, 'university-id');
    }

    public function passenger()
    {
        return $this->belongsTo(User::class, 'passenger-id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service-id');
    }

    public function rate()
    {
        return $this->hasOne(PassengerRate::class, 'user-id', 'passenger-id');
    }

    public function sugDriver()
    {
        return $this->hasOne(SugWeekDriver::class, 'booking-id', 'id');
    }
}
