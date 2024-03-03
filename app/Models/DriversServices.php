<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriversServices extends Model
{
    use HasFactory;

    protected $table = 'drivers-services';

    public $timestamps = false;

    protected $fillable = [
        'driver-id',
        'service-id',
        'date-of-add'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service-id');
    }
}
