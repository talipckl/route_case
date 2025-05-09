<?php

namespace App\Services\Location;

use App\Services\Location\LocationServiceInterface;
use App\Repositories\Location\LocationRepositoryInterface;
use App\Services\Route\RouteServiceInterface;

class LocationService implements LocationServiceInterface
{
    public function __construct(
        protected LocationRepositoryInterface $locationRepository,
        protected RouteServiceInterface $routeService
    )
    {
        //
    }

    public function getAllLocations()
    {
        return $this->locationRepository->getAllLocations();
    }

    public function getLocationById($id)
    {
        return $this->locationRepository->getLocationById($id);
    }

    public function createLocation(array $data)
    {
        return $this->locationRepository->createLocation($data);
    }

    public function updateLocation(array $data, $id)
    {
        return $this->locationRepository->updateLocation($data, $id);
    }

    public function deleteLocation($id)
    {
        return $this->locationRepository->deleteLocation($id);
    }

    public function getRoute($startLatitude, $startLongitude)
    {
        return $this->routeService->calculateRoute($startLatitude, $startLongitude);
    }
}