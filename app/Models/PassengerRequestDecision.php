<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PassengerRequestDecision extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action_type', // approve, reject
        'old_approval',
        'new_approval',
        'reason',
        'decided_by_employee_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'decided_by_employee_id');
    }
}
