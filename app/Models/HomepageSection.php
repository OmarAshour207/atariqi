<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomepageSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_key',
        'title',
        'content',
        'icon',
        'is_active',
    ];

     public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function getByKey($key)
    {
        return static::where('section_key', $key)->active()->first();
    }
}
