<?php

namespace Christie\Retail\Facades;

use Illuminate\Support\Facades\Facade;

class Retail extends Facade {

    public static function getFacadeAccessor() {
        return 'retail';
    }

}