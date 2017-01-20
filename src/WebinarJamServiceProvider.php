<?php

namespace Awolacademy\Webinarjam;

use App;
use Illuminate\Support\ServiceProvider;

class WebinarJamServiceProvider extends ServiceProvider
{
    protected $defer = false;

    public function boot()
    {
        $this->app->bind(WebinarJam::class, function() {
            return new WebinarJam(new JandjeWebinarJam(env('WJ_KEY')));
        });
    }

    public function register()
    {
        $this->app->bind('webinarjam', function() {
            return $this->app->make(WebinarJam::class);
        });
    }
}
