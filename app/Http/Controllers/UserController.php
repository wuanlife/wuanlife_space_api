<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Users\user_collection;

class UserController extends Controller
{
    //
    public function collect ($user_id)
    {
//        $token_user_id = 1;
//        echo "$user_id"."$token_user_id";
        $data = User_collection::all();
        dd ($data);

    }
}
