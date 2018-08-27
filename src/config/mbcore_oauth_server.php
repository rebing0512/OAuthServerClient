<?php

use Rebing0512\OAuthServerClient\Libraries\Helper;

return [
    'url' => 'http://www.pmcore.com',  //https://pmcore.mbcore.com

    'status' => 'on', // 状态，on 或者 off

    'roles' => [
//        '{appid}' => [
//            'secret' => '{secret_key}',
//            'channel' => '{channel_name}',        // 渠道：Andorid/iOS/weapp/weixin
//            'group_id'=>'{group_id}',             // 信息分组ID 国石/红木
//        ],
        'fZ4wruPFDWZTEwD1gUhbkez0CUmeWGJx' =>[
            'secret' => 'hgMSUCRPXlzncBtRrFJNcR2dRIIDNrZa',
            'channel' => 'weapp',
            'group_id' => '1',
        ],
    ],

    'timeout' => 1800, // 签名失效时间，单位: 秒

    'appStr' =>[
        '1' => 'guoshizhijia'  //test
    ]
];