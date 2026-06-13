<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformEmailLog extends Model
{
    use HasFactory;

    protected $table = 'platform_email_log';

    public $timestamps = true;

    protected $fillable = [
        'assigned_from_employee_id',
        'driver_id',
        'driver_email',
        'email_type',
        'status',
        'error_message'
    ];
}
