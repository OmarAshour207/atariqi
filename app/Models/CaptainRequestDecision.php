<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaptainRequestDecision extends Model
{
    use HasFactory;

    protected $table = 'captain_request_decisions';

    protected $fillable = [
        'user_id',
        'action_type',
        'old_approval',
        'new_approval',
        'reasondecided_by_employee_id',
        'reject_reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function employee()
    {
        return $this->belongsTo(Admin::class, 'reasondecided_by_employee_id');
    }
}
