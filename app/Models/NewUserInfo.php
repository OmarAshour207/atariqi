<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewUserInfo extends Model
{
    use HasFactory;

    protected $table = 'new-users-info';

    public $timestamps = false;

    protected $fillable = [
        'user-id',
        'user-first-name',
        'user-last-name',
        'phone-no',
        'gender',
        'email',
        'user-type',
        'image',
        'call-key-id',
        'user-stage-id',
        'university-id',
        'date-of-add',
        'date-of-edit'
    ];


    // Relations

    public function user()
    {
        return $this->belongsTo(User::class, 'user-id');
    }

    public function callingKey()
    {
        return $this->belongsTo(CallingKey::class, 'call-key-id');
    }

    public function university()
    {
        return $this->belongsTo(University::class, 'university-id');
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class, 'user-stage-id');
    }
}
