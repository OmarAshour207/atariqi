<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaptianRequestAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'assigned_from_employee_id',
        'assigned_to_employee_id',
        'note',
        'status'
    ];
}
