<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SkillController extends Controller
{
    public function index()
    {
        $skills = Skill::orderBy('id')
            ->get()
            ->transform(function ($obj) {
                return [
                    'id' => $obj->id,
                    'name' => $obj->name,
                ];
            });

        return response()->json([
            'status' => 1,
            'data' => $skills,
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

        $exists = Skill::whereRaw('LOWER(name) = ?', [strtolower($validated['name'])])->first();

        if ($exists) {
            return response()->json([
                'status' => 0,
                'message' => 'This skill already exists',
                'data' => [
                    'id' => $exists->id,
                    'name' => $exists->name,
                ],
            ], Response::HTTP_CONFLICT);
        }

        $skill = Skill::create($validated);

        return response()->json([
            'status' => 1,
            'message' => 'Skill created successfully',
            'data' => [
                'id' => $skill->id,
                'name' => $skill->name,
            ],
        ], Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $skill = Skill::find($id);
        if (!$skill) {
            return response()->json([
                'status' => 0,
                'message' => 'Skill not found',
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

        $exists = Skill::whereRaw('LOWER(name) = ?', [strtolower($validated['name'])])
            ->where('id', '!=', $id)
            ->first();

        if ($exists) {
            return response()->json([
                'status' => 0,
                'message' => 'This skill name already exists',
                'data' => [
                    'id' => $exists->id,
                    'name' => $exists->name,
                ],
            ], Response::HTTP_CONFLICT);
        }

        $skill->update([
            'name' => ucfirst(strtolower($validated['name'])),
        ]);

        return response()->json([
            'status' => 1,
            'message' => 'Skill updated successfully',
            'data' => [
                'id' => $skill->id,
                'name' => $skill->name,
            ],
        ], Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $skill = Skill::with(['freelancerSkills', 'workSkills'])->find($id);

        if (!$skill) {
            return response()->json([
                'status' => 0,
                'message' => 'Skill not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $hasRelations = $skill->freelancerSkills()->exists() || $skill->workSkills()->exists();

        if ($hasRelations) {
            return response()->json([
                'status' => 0,
                'message' => 'Cannot delete: This skill is associated with freelancers or works.',
            ], Response::HTTP_CONFLICT);
        }

        $skill->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Skill deleted successfully',
        ], Response::HTTP_OK);
    }

}
