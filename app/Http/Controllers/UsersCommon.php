<?php

namespace App\Http\Controllers;

use App\Models\Users\UsersBase;
use Illuminate\Http\Request;

class UsersCommon extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     * 2018/8/10 22:09---aunhappy
     * 搜索用户U6
     */
    public function get_users_search(Request $request)
    {
        $data = [
            'limit'     => $request->input('limit') ?? 20,     //每页显示数
            'offset'    => $request->input('offset') ?? 0,     //每页起始数
            'keyword'     => $request->input('keyword'),       //关键字
        ];
//        $users = UsersBase::search($data['keyword'])->paginate($data['limit'],'',$data['offset']/$data['limit']+1);
//
//        foreach ($users as $k=>$v) {
//            $rs['users'][$k]=[
//                "id"=>$v->id,
//                "name"=>$v->name,
//                "avatar_url"=>$v->avatar_url['url']
//            ];
//        }
//        $rs['total'] = $users->total();
        //dump(UsersBase::where('name','like',"%{$data['keyword']}%")->offset($data['offset'])->limit($data['limit'])->get());exit;
        $user = UsersBase::where('name','like',"%{$data['keyword']}%");
        $users = $user->offset($data['offset'])->limit($data['limit'])->get();
        foreach ($users as $k=>$v) {
            $rs['users'][$k]=[
                "id"=>$v->id,
                "name"=>$v->name,
                "avatar_url"=>$v->avatar_url['url']
            ];
        }
        $rs['total'] = $user->count();
        return $rs;
    }
}
