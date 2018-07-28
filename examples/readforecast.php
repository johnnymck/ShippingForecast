<?php

include_once 'vendor/autoload.php';

use johnnymck\ShippingForecast;

foreach ((new ShippingForecast())->getAll()['content'] as $zone) {
    print_r($zone['location'] . ":\n");
    print_r($zone['wind'] . "\n");
    print_r($zone['seas'] . "\n");
    print_r($zone['weather'] . "\n");
    print_r($zone['visibility'] . "\n");
    print_r("\n");
}
