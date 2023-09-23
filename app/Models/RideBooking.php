<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RideBooking extends Model
{
    use HasFactory;

    protected $table = 'ride-booking';

    public $timestamps = false;

    protected $fillable = [
        'passenger-id',
        'neighborhood-id',
        'lat',
        'lng',
        'location',
        'service-id',
        'action',
        'date-of-add'
    ];

    // relations
    public function passenger()
    {
        return $this->belongsTo(User::class, 'passenger-id');
    }

    public function neighborhood()
    {
        return $this->belongsTo(Neighbour::class, 'neighborhood-id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service-id');
    }
}
