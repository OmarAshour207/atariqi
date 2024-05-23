<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DelWeekInfo extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'del-week-info';

    protected $fillable = [
        'sug-id',
        'expect-arrived',
        'arrived-location',
        'arrived-destination',
        'passenger-rate',
        'allow-disabilities'
    ];

    public function ride()
    {
        return $this->belongsTo(SugWeekDriver::class, 'sug-id');
    }
}
