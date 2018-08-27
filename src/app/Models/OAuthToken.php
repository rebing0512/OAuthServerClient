<?php

namespace Rebing0512\OAuthServerClient\Models;

use Illuminate\Database\Eloquent\Model;

class OAuthToken extends Model
{


    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'oauth_token';



    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = ['id','group_id','access_token','expires_in'];

}