<?php

namespace App\Http\Controllers;

use App\Http\Requests\Location\StoreLocationRequest;
use App\Http\Requests\Location\UpdateLocationRequest;
use App\Services\Location\LocationServiceInterface;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    protected $service;

    /**
     * Create a new controller instance.
     * @param LocationServiceInterface $locationService
     */
    public function __construct(LocationServiceInterface $locationService)
    {
        $this->service = $locationService;
    }
    /**
     * Display a listing of the resource.
     * @param LocationServiceInterface $locationService
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $locations = $this->service->getAllLocations();
        return view('location.index', compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('location.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreLocationRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreLocationRequest $request)
    {
        $this->service->createLocation($request->all());
        return redirect()->route('location.index')->with('success', 'Konum başarıyla eklendi.');
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $location = $this->service->getLocationById($id);
        return view('location.show', compact('location'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $location = $this->service->getLocationById($id);
        return view('location.edit', compact('location'));
    }

    /**
     * Update the specified resource in storage.
     * @param UpdateLocationRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateLocationRequest $request, $id)
    {
        $location = $this->service->updateLocation($request->all(), $id);
        return redirect()->route('location.index')->with('success', 'Konum başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $location = $this->service->deleteLocation($id);
        return redirect()->route('location.index')->with('success', 'Konum başarıyla silindi.');
    }

    /**
     * Calculate the route between two points.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function calculateRoute(Request $request)
    {
        $startLatitude = $request->input('latitude');
        $startLongitude = $request->input('longitude');
        $route = $this->service->getRoute($startLatitude, $startLongitude);
        return response()->json($route);
    }
}
