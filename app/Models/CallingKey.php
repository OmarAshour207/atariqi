<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallingKey extends Model
{
    use HasFactory;

    protected $table = 'calling-key';

    public $timestamps = false;

    protected $fillable = [
        'call-key',
        'country',
        'date-of-add',
        'date-of-edit',
    ];
}
