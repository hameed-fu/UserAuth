<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    protected $fillable = [
        'userid', 'resettoken','tokenexpiry','dts'
    ];
    protected $table = 'password_resets';
    protected $primaryKey = 'passwordresetid';
    public $incrementing = false;
    public $timestamps = false;
}
