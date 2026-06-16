<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PassengerRequestAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'assigned_from_employee_id',
        'assigned_to_employee_id',
        'note',
        'status',
    ];

    public function passenger()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedFrom()
    {
        return $this->belongsTo(Admin::class, 'assigned_from_employee_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(Admin::class, 'assigned_to_employee_id');
    }
}
