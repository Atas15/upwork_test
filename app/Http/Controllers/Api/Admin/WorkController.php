<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Work;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WorkController extends Controller
{
    public function index()
    {
        $works = Work::with(['client','freelancer','profile'])->get();

        return response()->json([
            'status' => 1,
            'data' => [
                'works' => $works,
            ],
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id'        => 'required|exists:users,id',
            'freelancer_id'    => 'nullable|exists:users,id',
            'profile_id'       => 'nullable|exists:profiles,id',
            'title'            => 'required|string|max:255',
            'body'             => 'required|string',
            'experience_level' => 'nullable|integer|min:0|max:5',
            'job_type'         => 'nullable|integer|min:0|max:5',
            'price'            => 'nullable|integer|min:0',
            'number_of_proposals' => 'nullable|integer|min:0',
            'project_type'     => 'nullable|integer|min:0|max:5',
            'project_length'   => 'nullable|integer|min:0|max:5',
            'hours_per_week'   => 'nullable|integer|min:0|max:40',
            'last_viewed'      => 'nullable|date',
        ]);

        $work = Work::create($validated);

        return response()->json([
            'status' => 1,
            'message' => 'Work created successfully',
            'data' => [
                'work' => $work
            ]
        ], Response::HTTP_CREATED);
    }

    public function update(Request $request, Work $work)
    {
        $validated = $request->validate([
            'client_id'        => 'sometimes|exists:users,id',
            'freelancer_id'    => 'nullable|exists:users,id',
            'profile_id'       => 'nullable|exists:profiles,id',
            'title'            => 'sometimes|string|max:255',
            'body'             => 'sometimes|string',
            'experience_level' => 'nullable|integer|min:0|max:5',
            'job_type'         => 'nullable|integer|min:0|max:5',
            'price'            => 'nullable|integer|min:0',
            'number_of_proposals' => 'nullable|integer|min:0',
            'project_type'     => 'nullable|integer|min:0|max:5',
            'project_length'   => 'nullable|integer|min:0|max:5',
            'hours_per_week'   => 'nullable|integer|min:0|max:40',
            'last_viewed'      => 'nullable|date',
        ]);

        $work->update($validated);

        return response()->json([
            'status' => 1,
            'message' => 'Work updated successfully',
            'data' => [
                'work' => $work
            ]
        ], Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $work = Work::find($id);

        if (!$work) {
            return response()->json([
                'status' => 0,
                'message' => 'Work not found',
            ], Response::HTTP_NOT_FOUND);
        }

        // Burada “hasMany ilişkisi var” mesajı gösteriyoruz.
        // Gerçek kontrol yapılmıyor, sadece kullanıcıya hatırlatma.
        return response()->json([
            'status' => 1,
            'message' => 'Warning: This Work model has hasMany relations defined.',
        ], Response::HTTP_OK);

        // Eğer buna rağmen silmek istersen:
        // $work->delete();
        // return response()->json([
        //     'status' => 1,
        //     'message' => 'Work deleted successfully',
        // ], Response::HTTP_OK);
    }

}
