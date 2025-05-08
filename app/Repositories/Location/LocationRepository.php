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
     * Get the route between two points.
     * @param float $startLatitude
     * @param float $startLongitude
     * @return array
     */
    public function getRoute($startLatitude, $startLongitude)
    {
        // Tüm konumları al
        $locations = $this->location->all();
        $route = [];
        $remainingLocations = $locations;
        
        // Başlangıç noktasını ekle
        $startPoint = new \stdClass();
        $startPoint->latitude = $startLatitude;
        $startPoint->longitude = $startLongitude;
        $startPoint->name = 'Başlangıç Noktası';
        $route[] = $startPoint;
        
        // Mevcut konum olarak başlangıç noktasını kullan
        $currentLocation = $startPoint;
        
        // Tüm noktaları dolaşana kadar devam et
        while ($remainingLocations->count() > 0) {
            $nextLocation = null;
            $shortestDistance = PHP_INT_MAX;
            
            // Current location'a en yakın noktayı bul
            foreach ($remainingLocations as $location) {
                $distance = $this->calculateDistance(
                    $currentLocation->latitude,
                    $currentLocation->longitude,
                    $location->latitude,
                    $location->longitude
                );
                
                if ($distance < $shortestDistance) {
                    $shortestDistance = $distance;
                    $nextLocation = $location;
                }
            }
            
            if ($nextLocation) {
                // Bulunan en yakın noktayı rotaya ekle
                $route[] = $nextLocation;
                
                // Current location'u güncelle
                $currentLocation = $nextLocation;
                
                // Kullanılan lokasyonu listeden çıkar
                $remainingLocations = $remainingLocations->reject(function($location) use ($nextLocation) {
                    return $location->id === $nextLocation->id;
                });
            }
        }
        
        return [
            'route' => $route,
            'total_distance' => $this->calculateTotalRouteDistance($route)
        ];
    }

    // İki nokta arası mesafe hesaplama (Haversine formülü)
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Dünya'nın yarıçapı (km)
        
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);
        
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }

    // Toplam rota mesafesini hesapla
    private function calculateTotalRouteDistance($route)
    {
        $totalDistance = 0;
        
        for ($i = 0; $i < count($route) - 1; $i++) {
            $totalDistance += $this->calculateDistance(
                $route[$i]->latitude,
                $route[$i]->longitude,
                $route[$i + 1]->latitude,
                $route[$i + 1]->longitude
            );
        }
        
        return $totalDistance;
    }
}