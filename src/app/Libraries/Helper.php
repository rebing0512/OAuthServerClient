<?php
namespace Rebing0512\OAuthServerClient\Libraries;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class Helper
{
    /*
    * 生成随机字符串
    * @param int $length 生成随机字符串的长度
    * @param string $char 组成随机字符串的字符串
    * @return string $string 生成的随机字符串
    */
    public static function StrRand($length = 32, $char = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        if (!is_int($length) || $length < 0) {
            return false;
        }
        $string = '';
        for ($i = $length; $i > 0; $i--) {
            $string .= $char[mt_rand(0, strlen($char) - 1)];
        }
        return $string;
    }


    /**
     * @param Request $request
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public static function JsonRes($request, $data,$code = 1)
    {
        return response()->json([
            'code' => $code,
            'result' => $data,
        ], 200);
    }


    /**
     * @param Request $request
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public static function error_handler($request, $code = 403)
    {
        return response()->json([
            'msg' => 'Forbidden',
            'code' => $code
        ], 403);
    }

    /**
     * @param $secret_key
     * @param $echostr
     * @param $timestamp
     * @return string
     */
    public static function encrypting($app_id,$secret_key, $auth_token ,$TimeTokenInfo=[])
    {
        if(empty($TimeTokenInfo) ){
            $TimeTokenInfo = Helper::getTimeTokenInfo($auth_token);
        }
        $d = isset($TimeTokenInfo["d"])?$TimeTokenInfo["d"]:"0";
        if(!is_numeric($d )){$d=0;}
        $timestamp = isset($TimeTokenInfo["timestamp"])?$TimeTokenInfo["timestamp"]:time();
        if(!is_numeric($timestamp )){$timestamp=time();}
        $app_id_token = substr($app_id,$d-2);
        return md5($secret_key . $app_id_token .$auth_token.$timestamp);
    }

    /**
     * @param $secret_key
     * @param $signature
     * @param $server_signature
     * @return bool
     */
    public static function rule($secret_key, $signature, $server_signature)
    {
        return $secret_key.$signature === $server_signature;
    }


    public static function getTimeTokenInfo( $auth_token ){
        $timestamp = Carbon::now()->timestamp;
        $aH =  substr($auth_token,0,2);
        $a = sprintf('%02s',hexdec($aH));
        $d = -1*intval(substr($a,0,1));
        //当$d=0时，将$d设置为10
        if($d==0){$d=-10;}
        $lenght = strlen( $auth_token );
        //信息字符串长度必须大于16位
        if($lenght<16){ return false;}
        $str1 = substr($auth_token,$d);
        $str2 = substr($auth_token,2,$lenght-4+$d);
        $access_key = $str1.$str2;

        $cH =  substr($auth_token,$lenght-2+$d,2);
        $c = hexdec($cH);
        $b = sprintf('%02s',abs($c-$a)); //abs绝对值
        $timestamp = substr($timestamp,0,strlen($timestamp)-4).$b.$a;
        // $access_key   $timestamp
        $TimeTokenInfo = ["d"=>$d,"access_key"=>$access_key,"timestamp"=>$timestamp];

        // 如果解析错误返回false
        return $TimeTokenInfo;
    }

    private static function getTimeTokeArr($timestamp){
        //时间戳
        if(!$timestamp){
            $timestamp = Carbon::now()->timestamp;
        }
        //计算时间分组
        $a = substr($timestamp,-2);
        //此处对a做特殊处理，第一位如果是0则换成1
        $timestamp = $timestamp%10000;
        $timestamp = sprintf('%04s', $timestamp);
        $b = substr($timestamp,0,2);
        $c = $a+$b;
        $d = substr($a,0,1);
        //当$d=0时，将$d设置为10
        if($d==0){$d=10;}
        //转成十六进制
        $aH =  sprintf('%02s', dechex($a));
        $cH = sprintf('%02s', dechex($c));
        //生成返回值
        $r = ["d"=>$d,"a"=>$aH,"c"=>$cH];
        return $r;
    }

    public static function getAuthToke($access_key,$timestamp=0){
        //必须是 十位的正整数
        if(preg_match('/^[1-9][0-9]{9}$/',$timestamp)){$timestamp=0;}
        $TimeTokeArr = Helper::getTimeTokeArr($timestamp);
        //strlen 字符串长度
        $lenght = strlen($access_key );
        //信息字符串长度必须大于12位,否则重新生成
        if($lenght<12){ $access_key=Helper::StrRand();}
        $str1 = substr($access_key,0,$TimeTokeArr["d"]);
        $str2 = substr($access_key,$TimeTokeArr["d"],$lenght);
        $access_key = $TimeTokeArr["a"].$str2.$TimeTokeArr["c"].$str1;
        return $access_key;
    }

    public static function getAppIdToken($app_id,$auth_token){
        //strlen 字符串长度
        $lenght = strlen($app_id );
        //信息字符串长度必须大于12位,否则重新生成
        if($lenght<18){ $app_id=Helper::StrRand();}
        //获取auth_token的第两位
        $dH = substr($auth_token,1,1);
        $d = hexdec($dH);
        //if($d==0){$d=10;}
        $d=$d+1;
        $str1 = substr($app_id,0,$d);
        $str2 = substr($app_id,$d,$lenght);
        $str3 = sprintf('%02s', dechex($d+20));;
        $app_id_token = $str2.$str1.$str3;
        return $app_id_token;
    }

    public static function getAppId($app_id_toke){
        $dH = substr($app_id_toke,-2);
        $d =  hexdec($dH);
        if($d>20 && $d<38){
            $d = -1*($d - 20);
        }else{
            $d = -5;
        }
        //strlen 字符串长度
        $lenght = strlen($app_id_toke );
        //信息字符串长度必须大于12位,否则重新生成
        if($lenght<18){ $app_id_toke=Helper::StrRand();$lenght=18;}
        $app_id_toke = substr($app_id_toke,0,$lenght-2);
        $str1 = substr($app_id_toke,$d);
        $str2 = substr($app_id_toke,0,$lenght-2+$d);
        $app_id = $str1.$str2;
        return $app_id;
    }

    public static function calibrated($app_id_toke,$auth_token){
        $dH = substr($app_id_toke,-2);
        $d =  hexdec($dH);  //hexdec 解码成十进制  dechex  编码成十六进制
        $d = $d-21;
        $dH = dechex($d);
        $auth_dH = substr($auth_token,1,1);
        if($auth_dH == $dH){
            return true;
        }else{
            return false;
        }
    }


    /////////////////
    /**
     * @param Request $request
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public static function ErrorRes($msg , $code = 403)
    {
        return response()
             //->header('Access-Control-Allow-Origin', '*')
            // ->header('Access-Control-Allow-Headers', 'Origin, X-Requested-With,  Content-Type, Cookie, Accept, appid, channel, mbcore-access-token, mbcore-auth-token')   //multipart/form-data, application/json,
             //->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS')
             //->header('Access-Control-Allow-Credentials', 'false')
            ->json([
            'code' => $code,
            'msg' => $msg
        ], 403);
        //], 200);
    }

    // 签名函数
    public static function Sign( $request , $appid, $roles)
    {
        if(empty($appid) || !is_array($roles)) return '';
        //return "test";

        $request_all = $request->all();
        //dd($request_all);
        $timestamp = sprintf('%.0f', intval(strtotime($request_all['timestamp']))*1000 );  //毫秒级时间戳
        unset($request_all['sign'],$request_all['timestamp']); //去掉校验本身,时间戳

        $arr = array_merge($request_all,['appid'=>$appid,'timestamp'=>$timestamp],$roles);
        //return Helper::ErrorRes($arr);die();
        //dd($arr);
        //按照首字母大小写顺序排序
        sort($arr,SORT_STRING); //SORT_STRING - 把每一项作为字符串来处理。
        //dd($arr);
        //拼接成字符串
        $str = implode($arr);
        //dd($str);

        //进行加密
        $signature = sha1($str);
        //dd($signature);
        $signature = md5($signature);
        //dd($signature);
        //转换成大写
        $signature = strtoupper($signature);
        //dd($signature);
        return $signature;

    }

    public static function testGetSign( $arr , $appid, $roles, $echoDebug = 0)
    {
        if(empty($appid) || !is_array($roles)) return '';
        //return "test";

        $request_all = $arr;

        if($echoDebug){
            echo "timestamp 字符串：";
            var_dump($request_all['timestamp']);
        }

        //dd($request_all);
        $timestamp = sprintf('%.0f', intval(strtotime($request_all['timestamp']))*1000 );  //毫秒级时间戳
        unset($request_all['timestamp']); //去掉校验本身,时间戳

        $arr = array_merge($request_all,['appid'=>$appid,'timestamp'=>$timestamp],$roles);

        if($echoDebug){
            echo "timestamp 时间戳：";
            var_dump($timestamp);

            echo "全部：";
            var_dump($arr);
        }

        //按照首字母大小写顺序排序
        sort($arr,SORT_STRING); //SORT_STRING - 把每一项作为字符串来处理。

        if($echoDebug) {
            echo "sort排序：";
            var_dump($arr);
        }

        //dd($arr);
        //拼接成字符串
        $str = implode($arr);
        //dd($str);
        if($echoDebug) {
            echo "拼接成字符串：";
            var_dump($str);
        }

        //进行加密
        $signature = sha1($str);

        if($echoDebug) {
            echo "sha1：";
            var_dump($signature);
        }

        //dd($signature);
        $signature = md5($signature);

        if($echoDebug) {
            echo "md5：";
            var_dump($signature);
        }

        //dd($signature);
        //转换成大写
        $signature = strtoupper($signature);

        if($echoDebug) {
            echo "转换成大写：";
            var_dump($signature);
        }

        //dd($signature);
        return $signature;

    }

    //生成Token
    public static function Token()
    {
        $token = hash_hmac('sha1',Str::random(1000),Str::random(100));
        return $token;
    }

    public static function setAuthToken($request,$user_id){
        $token = Helper::Token();
        //$expires_in = 7200; 7200秒  2小时
        $expires_in = 604800;  //7天  7*24*60*60
        $access_token = $request->header('mbcore-access-token',false);  //
        if(!$access_token){
            return "---";
        }

        $tokenCache = [
            'expires_time' =>  Carbon::now()->timestamp + $expires_in,
            'user_id' => $user_id,
            'access_token' => $access_token
        ];
        Cache::store('redis_token')->put('mbcore_oauth_server_auth:' . $token, $tokenCache , $expires_in / 60);

        return $token;
    }


}