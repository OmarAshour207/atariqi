<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UniDrivingService extends Model
{
    use HasFactory;

    protected $table = 'uni-driving-service';

    public $timestamps = false;

    protected $fillable = [
        'university-id',
        'service-id',
        'date-of-add'
    ];

    // relations
    public function university()
    {
        return $this->belongsTo(University::class, 'university-id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service-id');
    }
}
