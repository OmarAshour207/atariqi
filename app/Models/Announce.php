<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announce extends Model
{
    protected $table = 'announce';

    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'title-ar',
        'title-eng',
        'contant-ar',
        'contant-eng',
        'date-of-add',
        'date-of-edit',
    ];

    public static function nextId(): int
    {
        return (int) (static::max('id') ?? 0) + 1;
    }
}
