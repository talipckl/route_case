<?php

use App\Services\Location\LocationService;
use App\Services\Location\LocationServiceInterface;
use App\Services\Route\RouteService;
use App\Services\Route\RouteServiceInterface;

return [
    LocationServiceInterface::class => LocationService::class,
    RouteServiceInterface::class => RouteService::class,
];


