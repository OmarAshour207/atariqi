<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DelDailyInfo extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'del-daily-info';

    protected $fillable = [
        'sug-id',
        'expect-arrived',
        'arrived-location',
        'arrived-destination',
        'passenger-rate',
        'driver-rate',
        'allow-disabilities'
    ];

    public function ride()
    {
        return $this->belongsTo(SugDayDriver::class, 'sug-id');
    }
}
