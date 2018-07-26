<?php
declare(strict_types=1);

namespace App\Providers;

use App\Handlers\AvatarHandler;
use Illuminate\Support\ServiceProvider;

/**
 * Class AvatarServiceProvider
 * @package App\Providers
 */
class AvatarServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(AvatarHandler::class, function ($app, $params) {
            return new AvatarHandler($params['file'], $params['user']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [AvatarHandler::class];
    }
}
