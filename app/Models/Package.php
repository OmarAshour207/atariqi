<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Package extends Model
{
    use HasFactory;

    const FREE = 0;
    const SOON = 1;
    const NEW = 2;
    const DISCOUNT = 3;

    protected $fillable = [
        'name_ar',
        'name_en',
        'price_monthly',
        'price_annual',
        'status',
    ];

    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            self::FREE => 'Free',
            self::SOON => 'Coming Soon',
            self::NEW => 'New',
            self::DISCOUNT => 'Discount',
            default => 'Unknown',
        };
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'package_features');
    }
}
