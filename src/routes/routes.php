<?php
use MBCore\OAuthServer\Middleware\MBCoreOAuth;
use MBCore\OAuthServer\Middleware\MBCoreToken;
use MBCore\OAuthServer\Middleware\MBCoreAuth;
use MBCore\OAuthServer\Middleware\MBCoreOAuthServer;

Route::get('/mbcore/apiauth/demo', 'DemoController@Demo');
Route::get('/mbcore/apiauth/test', 'DemoController@Test');

Route::group([
    'prefix' =>'oauth',
    'middleware'=>MBCoreOAuth::class
],function() {
    #
});


