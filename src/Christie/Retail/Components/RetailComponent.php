<?php

namespace Christie\Retail\Components;

class RetailComponent extends \Christie\Bones\Components\BonesComponent {

    public static $title = 'Retail';

    public $name = 'retail';

    public function configure() {
        // Nothing to see here
    }

    public static function hasSettings() {
        return true;
    }

}