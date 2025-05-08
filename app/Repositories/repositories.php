<?php

use App\Repositories\Location\LocationRepository;
use App\Repositories\Location\LocationRepositoryInterface;

return [
    LocationRepositoryInterface::class => LocationRepository::class,
];
