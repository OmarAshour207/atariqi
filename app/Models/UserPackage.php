<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPackage extends Model
{
    use HasFactory;

    const STATUS_ACTIVE = 1;
    const STATUS_EXPIRED = 2;
    const STATUS_CANCELLED = 3;

    protected $fillable = [
        'package_id',
        'user_id',
        'start_date',
        'end_date',
        'status',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Get the user that owns the subscription.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include active subscriptions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', UserPackage::STATUS_ACTIVE)
                    ->where('end_date', '>=', now());
    }

    /**
     * Check if subscription is active.
     */
    public function isActive(): bool
    {
        return $this->status === UserPackage::STATUS_ACTIVE && $this->end_date >= now();
    }

    /**
     * Check if subscription is expired.
     */
    public function isExpired(): bool
    {
        return $this->status === UserPackage::STATUS_EXPIRED || $this->end_date < now();
    }

}
