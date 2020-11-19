<?php

namespace Haruncpi\LaravelUserActivity;

use Haruncpi\LaravelUserActivity\Console\UserActivityDelete;
use Haruncpi\LaravelUserActivity\Console\UserActivityInstall;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/../config/user-activity.php';
    const ROUTE_PATH = __DIR__ . '/../routes';
    const VIEW_PATH = __DIR__ . '/../views';
    const ASSET_PATH = __DIR__ . '/../assets';
    const MIGRATION_PATH = __DIR__ . '/../migrations';


    private function publish()
    {
        $this->publishes([
            self::CONFIG_PATH => config_path('user-activity.php')
        ], 'config');

        $this->publishes([
            self::MIGRATION_PATH => database_path('migrations')
        ], 'migrations');
    }

    public function boot()
    {
        $this->publish();

        $this->loadRoutesFrom(self::ROUTE_PATH . '/web.php');
        $this->loadViewsFrom(self::VIEW_PATH, 'LaravelUserActivity');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'user-activity'
        );

        $this->app->register(EventServiceProvider::class);
        $this->commands([UserActivityInstall::class, UserActivityDelete::class]);
    }

}
