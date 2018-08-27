<?php
namespace	MBCore\OAuthServerClient;

use MBCore\OAuthServer\Console\Commands\Command;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Routing\Router;

//
class ServiceProvider extends BaseServiceProvider{

    /**
     * 在注册后进行服务的启动。
     *
     * @return void
     */
	public function boot()
	{
		// 【ok】【1】发布扩展包的配置文件
        $this->publishes([
                __DIR__.'/config/mbcore_oauth_server_client.php' => config_path('mbcore_oauth_server_client.php'),
        ], 'config');


        // 为控制器组指定公共的 PHP 命名空间
        $this->setupRoutes($this->app->router);


        // 【ok】【6】注册 Artisan 命令
        if ($this->app->runningInConsole()) {
            $this->commands([
                Command::class,
            ]);
        }


        // php artisan vendor:publish --tag=public --force
        // php artisan vendor:publish --tag=config


        // 【ok】【8】中间件发布
        $this->publishes([
            __DIR__.'/app/Http/Middleware/MBCoreOAuthServerClient.php' => app_path('Http/Middleware/MBCoreOAuthServerClient.php'),
        ], 'middleware');

	}

	/**
	 * 为控制器组指定公共的 PHP 命名空间
	 *
	 * @param  \Illuminate\Routing\Router  $router
	 * @return void
	 */
	public function setupRoutes(Router $router)
	{
		$router->group(['namespace' => 'MBCore\OAuthServerClient\Controllers'], function($router)
		{
			require __DIR__.'/routes/routes.php';
		});
	}

    /**
     * 在容器中注册绑定。
     *
     * @return void
     */
	public function register()
	{
	    // 默认的包配置
        $this->mergeConfigFrom(
            __DIR__.'/config/mbcore_oauth_server_client.php', 'mbcore_oauth_server_client'
        );
	}


}