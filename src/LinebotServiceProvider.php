<?php

namespace Jose13\LaravelLineBotLottery;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;

use Jose13\LaravelLineBotLottery\Controllers\LineWebhookController;

class LinebotServiceProvider extends ServiceProvider
{
    /**
     * @throws BindingResolutionException
     */
    public function register()
    {

        $this->app->make(LineWebhookController::class);

//        $this->mergeConfigFrom(
//            __DIR__.'/../config/LineBotServiceConfig.php', 'LineBotServiceConfig'
//        );
        $this->mergeConfigFrom(
            __DIR__.'/../config/LineBotServiceConfig.php', 'LineBotServiceConfig'
        );

    }

    /**
     * 執行服務註冊後啟動。
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom( __DIR__.'/../routes/api.php');

        $this->publishes([
            __DIR__.'/../config/LineBotServiceConfig.php' => config_path('LineBotServiceConfig.php'),
        ]);

    }
}
