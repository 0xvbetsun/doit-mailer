<?php
declare(strict_types=1);

namespace App\Providers;

use App\Handlers\GithubUsersHandler;
use Illuminate\Support\ServiceProvider;

/**
 * Class GithubUsersServiceProvider
 * @package App\Providers
 */
class GithubUsersServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(GithubUsersHandler::class, function ($app, $params) {
            return new GithubUsersHandler($params['usernames']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [GithubUsersHandler::class];
    }
}
