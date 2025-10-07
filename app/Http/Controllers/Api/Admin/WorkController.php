<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Work;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WorkController extends Controller
{
    public function index()
    {
        $works = Work::orderBy('id', 'desc')
            ->get()
            ->transform(function ($obj) {
                return [
                    'id' => $obj->id,
                    'client_id' => $obj->client_id,
                    'freelancer_id' => $obj->freelancer_id,
                    'profile_id' => $obj->profile_id,
                    'title' => $obj->title,
                    'body' => $obj->body,
                ];
            });

        return response()->json([
            'status' => 1,
            'data' => $works,
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id'      => 'required|exists:users,id',
            'freelancer_id'  => 'nullable|exists:users,id',
            'profile_id'     => 'nullable|exists:profiles,id',
            'title'          => 'required|string|max:255',
            'body'           => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 0,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $work = Work::create($validator->validated());

        return response()->json([
            'status'  => 1,
            'message' => 'Work created successfully',
            'data'    => [
                'work' => $work
            ]
        ], Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $work = Work::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'client_id'        => 'sometimes|exists:users,id',
            'freelancer_id'    => 'nullable|exists:users,id',
            'profile_id'       => 'nullable|exists:profiles,id',
            'title'            => 'sometimes|string|max:255',
            'body'             => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 0,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validated = $validator->validated();
        $work->update($validated);

        return response()->json([
            'status'  => 1,
            'message' => 'Work updated successfully',
            'data'    => $validated
        ], Response::HTTP_OK);
    }


    public function destroy($id)
    {
        $work = Work::with(['proposals', 'workSkills'])->find($id);

        if (!$work) {
            return response()->json([
                'status' => 0,
                'message' => 'Work not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $hasRelations = $work->proposals()->exists() || $work->workSkills()->exists();

        if ($hasRelations) {
            return response()->json([
                'status' => 0,
                'message' => 'Cannot delete: This work is associated with proposals or skills.',
            ], Response::HTTP_CONFLICT);
        }

        $work->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Work deleted successfully',
        ], Response::HTTP_OK);
    }
}
