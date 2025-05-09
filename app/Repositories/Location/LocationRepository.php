<?php

namespace App\Repositories\Location;

use App\Models\Location;

class LocationRepository implements LocationRepositoryInterface
{
    /**
     * @var Location
     */
    protected $location;

    /**
     * Create a new repository instance.
     * @param Location $location
     */
    public function __construct(Location $location)
    {
       $this->location = $location;
    }

    /**
     * Get all locations.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllLocations()
    {
        return $this->location->all();
    }

    /**
     * Get a location by ID.
     * @param int $id
     * @return \App\Models\Location|null
     */
    public function getLocationById($id)
    {
        return $this->location->findOrFail($id);
    }

    /**
     * Create a new location.
     * @param array $data
     * @return \App\Models\Location
     */
    public function createLocation(array $data)
    {
        return $this->location->create($data);
    }

    /**
     * Update a location.
     * @param array $data
     * @param int $id
     * @return \App\Models\Location
     */ 
    public function updateLocation(array $data, $id)
    {
        $location = $this->location->findOrFail($id);
        $location->update($data);
        return $location->fresh();
    }

    /**
     * Delete a location.
     * @param int $id
     * @return bool
     */
    public function deleteLocation($id)
    {
        $location = $this->location->find($id);
        return $location->delete();
    }

    /**
     * Get route between points
     * @param float $startLatitude
     * @param float $startLongitude
     * @return array
     */
    public function getRoute($startLatitude, $startLongitude)
    {
        return [];
    }
}