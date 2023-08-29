<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLogin extends Model
{
    use HasFactory;

    protected $table = 'user-login';

    public $timestamps = false;
    protected $fillable = [
        'user-id',
        'date-time',
        'login-logout'
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class, 'user-id');
    }
}
