<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = ['name', 'code'];

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Admin::class, 'employee_perm', 'permission_id', 'employee_id')
            ->withTimestamps();
    }
}
