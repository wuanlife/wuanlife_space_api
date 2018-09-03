<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;

class User_collection extends Model
{
    public $timestamps = false;

    protected $dates = [
        'create_at'
    ];
}
