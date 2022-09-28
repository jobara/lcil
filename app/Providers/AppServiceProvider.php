<?php

namespace App\Providers;

use Composer\InstalledVersions;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;
use Spatie\LaravelIgnition\Facades\Flare as FacadesFlare;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(UrlGenerator $url)
    {
        if (config('app.env') !== 'local') {
            $url->forceScheme('https');
        }

        FacadesFlare::determineVersionUsing(function () {
            return InstalledVersions::getRootPackage()['pretty_version'];
        });

        Paginator::defaultView('components.pagination-links');

        Paginator::defaultSimpleView('components.simple-pagination-links');
    }
}
