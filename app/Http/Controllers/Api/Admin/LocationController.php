<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::orderBy('name')
            ->get()
            ->transform(function ($obj) {
                return [
                    'id' => $obj->id,
                    'name' => $obj->name,
                ];
            });

        return response()->json([
            'status' => 1,
            'data' => $locations,
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 0,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validated = $validator->validated();

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

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 0,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validated = $validator->validated();

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
        $location = Location::with(['clients', 'freelancers'])->find($id);

        if (!$location) {
            return response()->json([
                'status' => 0,
                'message' => 'Location not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $hasRelations = $location->clients()->exists() || $location->freelancers()->exists();

        if ($hasRelations) {
            return response()->json([
                'status' => 0,
                'message' => 'Cannot delete: This Location has related clients or freelancers.',
            ], Response::HTTP_CONFLICT);
        }

        $location->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Location deleted successfully',
        ], Response::HTTP_OK);
    }


}
