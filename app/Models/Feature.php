<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasFactory;

    public $timestamps = false;

     protected $fillable = [
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'service_id'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_features');
    }

}
