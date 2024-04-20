<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DayUnrideRate extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'day-unride-rate';

    protected $fillable = [
        'sug-id',
        'comment',
        'rate'
    ];

    // relations
    public function ride()
    {
        return $this->belongsTo(SugDayDriver::class, 'sug-id');
    }
}
