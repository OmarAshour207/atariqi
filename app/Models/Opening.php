<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opening extends Model
{
    use HasFactory;

    protected $table = 'opening';

    public $timestamps = false;

    protected $fillable = [
        'title-ar',
        'title-eng',
        'contant-ar',
        'contant-eng',
        'date-of-add',
        'date-of-edit',
    ];
}
