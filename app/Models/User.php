<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public $timestamps = false;
    protected $fillable = [
        'user-first-name',
        'user-last-name',
        'phone-no',
        'gender',
        'university-id',
        'user-stage-id',
        'call-key-id',
        'email',
        'approval',
        'user-type',
        'image',
        'date-of-add',
        'date-of-edit',
        'code',
        'fcm_token'
    ];

    protected $hidden = [
        'remember_token',
//        'code'
    ];

    // Relations
    public function callingKey()
    {
        return $this->belongsTo(CallingKey::class, 'call-key-id');
    }

    public function university()
    {
        return $this->belongsTo(University::class, 'university-id');
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class, 'user-stage-id');
    }

    public function driverInfo()
    {
        return $this->hasOne(DriverInfo::class, 'driver-id', 'id');
    }

    public function driverCar()
    {
        return $this->hasOne(DriversCar::class, 'driver-id', 'id');
    }

    public function driverNeighborhood()
    {
        return $this->hasOne(DriverNeighborhood::class, 'driver-id', 'id');
    }
    public function driverSchedule()
    {
        return $this->hasOne(DriverSchedule::class, 'driver-id', 'id');
    }
    public function driverService()
    {
        return $this->hasMany(DriversServices::class, 'driver-id', 'id');
    }
}
