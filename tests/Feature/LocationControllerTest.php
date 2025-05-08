<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class LocationControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_index_page_can_be_rendered()
    {
        $response = $this->get(route('location.index'));
        $response->assertStatus(200);
        $response->assertViewIs('location.index');
    }

    public function test_create_page_can_be_rendered()
    {
        $response = $this->get(route('location.create'));
        $response->assertStatus(200);
        $response->assertViewIs('location.create');
    }

    public function test_location_can_be_stored()
    {
        $locationData = [
            'name' => 'Test Location',
            'color' => '#FF0000',
            'latitude' => 41.0082,
            'longitude' => 28.9784
        ];

        $response = $this->post(route('location.store'), $locationData);
        
        $response->assertRedirect(route('location.index'));
        $this->assertDatabaseHas('locations', ['name' => 'Test Location']);
    }

    public function test_location_can_be_updated()
    {
        $location = Location::factory()->create();
        
        $updatedData = [
            'name' => 'Updated Location',
            'color' => '#00FF00',
            'latitude' => 41.0082,
            'longitude' => 28.9784
        ];

        $response = $this->put(route('location.update', $location->id), $updatedData);
        
        $response->assertRedirect(route('location.index'));
        $this->assertDatabaseHas('locations', ['name' => 'Updated Location']);
    }

    public function test_location_can_be_deleted()
    {
        $location = Location::factory()->create();

        $response = $this->delete(route('location.destroy', $location->id));
        
        $response->assertRedirect(route('location.index'));
        $this->assertSoftDeleted('locations', ['id' => $location->id]);
    }
}
