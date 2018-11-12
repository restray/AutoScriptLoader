<?php

namespace Restray\AutoScriptLoader\Facades;

use Illuminate\Support\Facades\Facade;

class AutoScriptLoader extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'autoscriptloader';
    }
}
