<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Location;
use App\Services\Location\LocationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LocationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $locationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->locationService = app(LocationService::class);
    }

    public function test_can_get_all_locations()
    {
        Location::factory()->count(3)->create();

        $locations = $this->locationService->getAllLocations();

        $this->assertCount(3, $locations);
    }

    public function test_can_create_location()
    {
        $locationData = [
            'name' => 'Test Location',
            'color' => '#FF0000',
            'latitude' => 41.0082,
            'longitude' => 28.9784
        ];

        $location = $this->locationService->createLocation($locationData);

        $this->assertInstanceOf(Location::class, $location);
        $this->assertEquals('Test Location', $location->name);
    }

    public function test_can_update_location()
    {
        $location = Location::factory()->create();

        $updatedData = [
            'name' => 'Updated Location',
            'color' => '#00FF00',
            'latitude' => 41.0082,
            'longitude' => 28.9784
        ];

        $updatedLocation = $this->locationService->updateLocation($updatedData, $location->id);

        $this->assertEquals('Updated Location', $updatedLocation->name);
    }

    public function test_can_delete_location()
    {
        $location = Location::factory()->create();

        $this->locationService->deleteLocation($location->id);

        $this->assertSoftDeleted('locations', ['id' => $location->id]);
    }

    public function test_can_calculate_route()
    {
        $startLatitude = 41.0082;
        $startLongitude = 28.9784;

        $route = $this->locationService->getRoute($startLatitude, $startLongitude);

        $this->assertIsArray($route);
    }
}
