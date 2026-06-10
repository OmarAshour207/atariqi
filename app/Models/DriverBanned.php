<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverBanned extends Model
{
    use HasFactory;

    protected $table = 'drivers-banned';

    protected $fillable = [
        'assigned_from_employee_id',
        'driver_identity',
        'driver_no',
        'driver_car_no',
        'note',
    ];


    public function employee()
    {
        return $this->belongsTo(User::class, 'assigned_from_employee_id');
    }
}
