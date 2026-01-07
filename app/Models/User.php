<?php

namespace App\Models;

use Carbon\Carbon;
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
        'password',
        'is_admin',
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

    // Scope
    public function scopeCheckAcceptTrips($due): bool
    {
        if ($due <= 50) {
            return true;
        }

        $firstReminder = $this->paymentReminders()
            ->select('created_at')
            ->where('driver-id', $this->id)
            ->whereRaw("DATEDIFF(CURDATE(), created_at) >= 7")
            ->first();

        if ($firstReminder) {
            return false;
        }

        return true;
    }

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

    public function paymentReminders()
    {
        return $this->hasMany(PaymentReminder::class, 'driver-id', 'id');
    }

    public function packages()
    {
        return $this->hasMany(UserPackage::class);
    }

    public function activePackage()
    {
        return $this->hasOneThrough(
        Package::class,        // The final model we want to access
        UserPackage::class,    // The intermediate model
        'user_id',             // Foreign key on UserPackage table
        'id',                  // Foreign key on Package table
        'id',                  // Local key on User table
        'package_id'           // Local key on UserPackage table
        )->where('user_packages.status', UserPackage::STATUS_ACTIVE)
        ->where('user_packages.end_date', '>=', now())
        ->latest('user_packages.created_at');
    }
}
