<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PassengerBanned extends Model
{
    use HasFactory;

    public $table = 'passenger_banned';

    protected $fillable = [
        'assigned_from_employee_id',
        'passenger_identity',
        'passenger_no',
        'note'
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'assigned_from_employee_id');
    }
}
