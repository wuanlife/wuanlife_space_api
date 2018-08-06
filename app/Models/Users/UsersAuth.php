<?php

namespace App\Models\Users;


use Illuminate\Database\Eloquent\Model;

class UsersAuth extends Model
{
    protected $table = 'users_auth';

    function auth()
    {
        return $this->hasOne(AuthDetail::class, 'id', 'auth');
    }
}