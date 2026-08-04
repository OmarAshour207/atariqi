<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UniDrivingService extends Model
{
    use HasFactory;

    protected $table = 'uni-driving-services';

    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'university-id',
        'service-id',
        'date-of-add'
    ];

    public static function nextId(): int
    {
        return (int) (static::max('id') ?? 0) + 1;
    }

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
