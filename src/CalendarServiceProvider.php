<?php

namespace Infinety\Calendar;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Infinety\Calendar\Engine\CalendarEventsEngine;
use Infinety\Calendar\Models;
use Infinety\Calendar\Services\CalendarEventsService;

/**
 * Service provider class
 *
 * @package Infinety\Calendar
 * @author Eric Lagarda <eric@infinety.es>
 */
class CalendarServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang/', 'calendar-events');

        $this->loadViewsFrom(__DIR__ . '/../resources/views/', 'calendar-events');

        $this->publishes(
            [
                __DIR__ . '/../database/migrations/' => database_path('migrations/'),
                __DIR__ . '/../resources/lang/' => base_path('resources/lang/'),
                __DIR__ . '/../resources/views/' => base_path('resources/vendor/calendar-events/'),
                __DIR__ . '/../public/js/' => public_path('/js/'),
            ]
        );

        /**
         * Publish config file
         */
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('calendar.php')
        ], 'config');

        Validator::extend('time', '\Infinety\Calendar\Validator\CalendarValidator@validateTime');
        Validator::extend(
            'dates_array',
            '\Infinety\Calendar\Validator\CalendarValidator@validateDatesArray'
        );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/config.php',
            'calendar'
        );

        $this->app->bind(
            'calendar_engine',
            function () {
                return new CalendarEngine(new Carbon());
            }
        );

        $this->app->bind(
            'calendar_service',
            function () {
                new CalendarService(
                    $this->app->make('calendar_engine'),
                    new Models\Event(),
                    new Models\EventsModelsColor(),
                    new Cache()
                );
            }
        );
    }
}
