<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WebPage extends Model
{
    protected $table = 'web_pages';

    protected $fillable = [
        'name',
        'route',
        'parent_id',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Admin::class, 'employee_page', 'page_id', 'employee_id')
            ->withTimestamps();
    }
}
