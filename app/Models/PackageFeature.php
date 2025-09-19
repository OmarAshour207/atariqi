<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageFeature extends Model
{
    use HasFactory;

    protected $table = 'package_features';

    public $timestamps = false;

    protected $fillable = [
        'package_id',
        'feature_id',
    ];

}
