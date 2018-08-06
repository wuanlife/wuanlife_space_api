<?php

namespace App\Http\Middleware;


use App\Models\Users\UsersAuth;
use Closure;

class VerifyAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $id_token = $request->header('ID-Token');
            $id_token = json_decode(base64_decode(explode('.', $id_token)[1]));
            $user_id = $id_token->uid;

            $user = UsersAuth::find($user_id);
            $auth = $user->auth()->whereIn('identity', ['管理员','最高管理员'])->count();
            if (!$auth) {
                return response(['error' => 'Insufficient permissions,need administrator rights'], 403);
            }
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 400);
        }
        return $next($request);
    }
}
