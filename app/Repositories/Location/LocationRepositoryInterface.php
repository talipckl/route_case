<?php

namespace App\Repositories\Location;

interface LocationRepositoryInterface
{
    // Get all locations
    public function getAllLocations();

    // Get location by id
    public function getLocationById($id);

    // Create location
    // @param array $data
    public function createLocation(array $data);

    // Update location
    // @param array $data
    // @param int $id
    /**
     * Update a location.
     * @param array $data
     * @param int $id
     * @return \App\Models\Location
     */
    public function updateLocation(array $data, $id);

    // Delete location
    // @param int $id
    public function deleteLocation($id);

    // Get route
    public function getRoute($startLatitude, $startLongitude);
}