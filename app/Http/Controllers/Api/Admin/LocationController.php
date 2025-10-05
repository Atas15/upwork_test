<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocationController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 1,
            'data' => [
                'locations' => Location::get(),
            ],
        ], Response::HTTP_OK);
    }

    public function create(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $exists = Location::whereRaw('LOWER(name) = ?', [strtolower($validated['name'])])->first();

        if ($exists) {
            return response()->json([
                'status' => 0,
                'message' => 'This location already exists',
                'data' => [
                    'id' => $exists->id,
                    'name' => $exists->name,
                ],
            ], Response::HTTP_CONFLICT);
        }

        $location = Location::create($validated);

        return response()->json([
            'status' => 1,
            'message' => 'Location created successfully',
            'data' => [
                'id' => $location->id,
                'name' => $location->name,
            ],
        ], Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $location = Location::find($id);
        if (!$location) {
            return response()->json([
                'status' => 0,
                'message' => 'Location not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $exists = Location::whereRaw('LOWER(name) = ?', [strtolower($validated['name'])])
            ->where('id', '!=', $id)
            ->first();

        if ($exists) {
            return response()->json([
                'status' => 0,
                'message' => 'This location name already exists',
                'data' => [
                    'id' => $exists->id,
                    'name' => $exists->name,
                ],
            ], Response::HTTP_CONFLICT);
        }

        $location->update([
            'name' => ucfirst(strtolower($validated['name'])),
        ]);

        return response()->json([
            'status' => 1,
            'message' => 'Location updated successfully',
            'data' => [
                'id' => $location->id,
                'name' => $location->name,
            ],
        ], Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $location = Location::find($id);
        if (!$location) {
            return response()->json([
                'status' => 0,
                'message' => 'Location not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $location->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Location deleted successfully',
        ], Response::HTTP_OK);
    }

}
