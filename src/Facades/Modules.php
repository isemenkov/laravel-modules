<?php

namespace isemenkov\Modules\Facades;

use isemenkov\Modules\ModulesManager;
use Illuminate\Support\Facades\Facade;

class Modules extends Facade {
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \isemenkov\Modules\ModulesManager::class;
    }
}