<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeekRideBooking extends Model
{
    use HasFactory;

    protected $table = 'week-ride-booking';

    public $timestamps = false;

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
    ];

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
}
