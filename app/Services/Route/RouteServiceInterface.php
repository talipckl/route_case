<?php

namespace App\Services\Route;

interface RouteServiceInterface
{
    /**
     * Calculate route from starting point to all locations
     * @param float $startLatitude
     * @param float $startLongitude
     * @return array
     */
    public function calculateRoute($startLatitude, $startLongitude);
} 