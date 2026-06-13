<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeePackageLog extends Model
{
    use HasFactory;

    protected $table = 'employee_package_logs';

    public $timestamps = true;

    protected $fillable = [
        'assigned_from_employee_id',
        'driver_id',
        'id_package_old',
        'id_package_new',
        'status',
    ];
}
