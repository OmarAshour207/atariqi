<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuggestionDriver extends Model
{
    use HasFactory;

    protected $table = 'suggestions-drivers';

    public $timestamps = false;

    protected $fillable = [
        'booking-id',
        'driver-id',
        'action',
        'date-of-add',
        'date-of-edit'
    ];
}
