<?php

namespace Rebing0512\OAuthServer\Console\Commands;

use Illuminate\Console\Command as BaseCommand;
use MBCore\OAuthServer\Libraries\Helper;

class Command extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rebing0512:oauth_server_client';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[Rebing0512 OAuth Server Client] 生成随机 appid  和 secret 。';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //输出信息
        $this->info('Rebing0512 OAuth Server Client:');
        $this->info('appid: ' . Helper::StrRand());
        $this->info('secret: ' . Helper::StrRand());
    }
}