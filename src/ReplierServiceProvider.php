<?php

namespace Viodev;

use Illuminate\Support\ServiceProvider;

class ReplierServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Replier::class, function () {
            return new Replier(resolve('translator'), config('replier.lang_prefix'));
        });

        $this->app->singleton(Pager::class, function () {
            return new Pager(resolve('request'), config('pager.limit'), config('pager.order'));
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                TransformerMakeCommand::class,
            ]);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/replier.php' => config_path('replier.php'),
        ]);
        $this->publishes([
            __DIR__ . '/../config/pager.php' => config_path('pager.php'),
        ]);
        $this->publishes([
            __DIR__ . '/../resources/lang/en/replies.php' => base_path('resources/lang/en/replies.php'),
        ]);
    }
}