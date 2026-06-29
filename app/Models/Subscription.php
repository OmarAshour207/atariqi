<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    public const TYPE_GENERAL_DUES_PERCENTAGE = 2;

    public $timestamps = false;

    protected $fillable = [
        'name-ar',
        'name-eng',
        'cost',
        'type'
    ];

    public static function generalDuesPercentage(): ?self
    {
        return static::where('type', self::TYPE_GENERAL_DUES_PERCENTAGE)->first();
    }

    public static function generalDuesPercentageValue(): float
    {
        return (float) (static::generalDuesPercentage()?->cost ?? 0);
    }
}
