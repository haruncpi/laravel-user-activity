<?php

namespace Haruncpi\LaravelUserActivity;

use Haruncpi\LaravelUserActivity\Events\RequestTerminatedEvent;
use Haruncpi\LaravelUserActivity\Listeners\LockoutListener;
use Haruncpi\LaravelUserActivity\Listeners\LoginListener;
use Haruncpi\LaravelUserActivity\Listeners\RequestTerminatedListener;
use Haruncpi\LaravelUserActivity\Listeners\RouteMatchListener;
use Haruncpi\LaravelUserActivity\Listeners\RoutingListener;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Routing\Events\RouteMatched;

use Illuminate\Routing\Events\Routing;


class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Login::class   => [
            LoginListener::class
        ],
        Lockout::class => [
            LockoutListener::class
        ],

        RouteMatched::class => [
            RouteMatchListener::class
        ],

        RequestTerminatedEvent::class => [
            RequestTerminatedListener::class
        ]
    ];

    public function boot()
    {
        parent::boot();
    }
}