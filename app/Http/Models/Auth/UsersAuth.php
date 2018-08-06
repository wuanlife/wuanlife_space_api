<?php

namespace App\Http\Models\Auth;

use Illuminate\Database\Eloquent\Model;

class UsersAuth extends Model
{
    public function auth()
    {
        return $this->hasOne(AuthDetail::class, 'auth', 'id');
    }
}
