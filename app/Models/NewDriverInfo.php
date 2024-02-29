<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewDriverInfo extends Model
{
    use HasFactory;

    protected $table = 'new-driver-info';

    public $timestamps = false;

    protected $fillable = [
        'driver-id',
        'car-brand',
        'car-model',
        'car-number',
        'car-letters',
        'car-color',
        'driver-neighborhood',
        'driver-rate',
        'driver-license-link',
        'allow-disabilities',
        'date-of-add',
        'date-of-edit'
    ];


    // relations
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver-id');
    }
}
