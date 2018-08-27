<?php

namespace Rebing0512\OAuthServerClient\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use RuntimeException;
use Rebing0512\OAuthServerClient\Libraries\Helper;
use Illuminate\Support\Facades\Cache;

class MBCoreToken
{
    public function handle($request, Closure $next)
    {

            if($request->isMethod('options')){
                // 要执行的代码
                return response("ok");
            }
            //dd("token middle");

            // 头文件
            $appid = $request->header('appid',false);  //
            $access_token = $request->header('mbcore-access-token',false);  //

            if (!$appid  || !$access_token) {
                //call_user_func_array — 调用回调函数，并把一个数组参数作为回调函数的参数
                return Helper::ErrorRes("Headers Err!",403.1);
                // 缺少请求头
            }

            $data = Cache::store('redis_token')->get('mbcore_oauth_server_token:' . $access_token);
            if (is_null($data)) {
                return Helper::ErrorRes("access-token Err!",403.2);
            }

            $ip = $request->getClientIp();
            if($ip != $data['ip']){
                return Helper::ErrorRes("IP Err!",403.3);
            }

            if($appid != $data['appid']){
                return Helper::ErrorRes("AppID Err!",403.4);
            }

            // 请求是否超时
            $timestamp = intval($data['expires_time']);
            //dd($timeout);
            if (time() - $timestamp > 0) {
                return Helper::ErrorRes("expires time Err!",403.5);
                // 签名失效
            }

            $group_id = $data['group_id'];

            $request->offsetSet('group_id', $group_id);


        return $next($request);
    }
}