<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImmediateUnrideRate extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'immediate-unride-rate';

    protected $fillable = [
        'sug-id',
        'comment',
        'rate'
    ];

    protected $casts = [
        'rate'  => 'double'
    ];

    // relations
    public function ride()
    {
        return $this->belongsTo(SuggestionDriver::class, 'sug-id');
    }
}
