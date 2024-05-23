<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeekUnrideRate extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'week-unride-rate';

    protected $fillable = [
        'sug-id',
        'comment',
        'rate'
    ];

    // relations
    public function ride()
    {
        return $this->belongsTo(SugWeekDriver::class, 'sug-id');
    }
}
