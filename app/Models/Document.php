<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $table = 'document';

    public $timestamps = false;

    protected $fillable = [
        'title-ar',
        'title-eng',
        'file-link',
        'date-of-add',
        'date-of-edit',
    ];

    protected function fileLink(): Attribute
    {
        return Attribute::make(
            get: fn(string $value)  => url($value)
        );
    }
}
