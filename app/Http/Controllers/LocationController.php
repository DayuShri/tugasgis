<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;

class LocationController extends Controller {
    public function store(Request $request)
    {
    $request->validate([
        'location_name' => 'required|string',
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
    ]);

    Location::create([
        'location_name' => $request->location_name,
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
    ]);

    return response()->json([
        'message' => 'Lokasi berhasil disimpan!',
    ], 201); // 201 Created
    }
}