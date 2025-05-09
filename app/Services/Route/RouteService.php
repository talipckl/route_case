<?php

namespace App\Services\Route;

use App\Repositories\Location\LocationRepositoryInterface;

class RouteService implements RouteServiceInterface
{
    public function __construct(
        protected LocationRepositoryInterface $locationRepository
    ) {}

    /**
     * Calculate route from starting point to all locations
     * @param float $startLatitude
     * @param float $startLongitude
     * @return array
     */
    public function calculateRoute($startLatitude, $startLongitude)
    {
        $locations = $this->locationRepository->getAllLocations();
        $route = [];
        $remainingLocations = $locations;
        
        $startPoint = new \stdClass();
        $startPoint->latitude = $startLatitude;
        $startPoint->longitude = $startLongitude;
        $startPoint->name = 'Başlangıç Noktası';
        $route[] = $startPoint;
        
        $currentLocation = $startPoint;
        
        while ($remainingLocations->count() > 0) {
            $nextLocation = null;
            $shortestDistance = PHP_INT_MAX;
            
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
                $route[] = $nextLocation;
                $currentLocation = $nextLocation;
                
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

    /**
     * Calculate distance between two points using Haversine formula
     */
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

    /**
     * Calculate total distance of the route
     */
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