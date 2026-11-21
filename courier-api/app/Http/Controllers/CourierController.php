<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourierRequest;
use App\Http\Requests\UpdateCourierRequest;
use App\Models\Courier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['sometimes', 'string', 'max:150'],
            'level' => ['sometimes', 'string', 'regex:/^[1-5](,[1-5])*$/'],
            'sort' => ['sometimes', 'in:registered_at'],
            'direction' => ['sometimes', 'in:asc,desc'],
            'per_page' => ['sometimes', 'integer', 'between:1,100'],
        ]);

        $query = Courier::query();
        if (!empty($validated['search'])) {
            // All search words must occur in the name; their order need not match.
            foreach (preg_split('/\\s+/', trim($validated['search'])) as $term) {
                if ($term !== '') {
                    $query->where('name', 'like', '%' . addcslashes($term, '%_\\') . '%');
                }
            }
        }
        if (!empty($validated['level'])) {
            $query->whereIn('level', array_map('intval', explode(',', $validated['level'])));
        }

        $direction = $validated['direction'] ?? 'asc';
        if (($validated['sort'] ?? null) === 'registered_at') {
            $query->orderBy('registered_at', $direction);
        } else {
            $query->orderBy('name', $direction);
        }

        return response()->json($query->paginate($validated['per_page'] ?? 15)->withQueryString());
    }

    public function store(StoreCourierRequest $request): JsonResponse
    {
        $courier = Courier::create($request->validated());
        return response()->json($courier, 201);
    }

    public function show(Courier $courier): JsonResponse
    {
        return response()->json($courier);
    }

    public function update(UpdateCourierRequest $request, Courier $courier): JsonResponse
    {
        $courier->update($request->validated());
        return response()->json($courier->refresh());
    }

    public function destroy(Courier $courier): JsonResponse
    {
        $courier->delete();
        return response()->json(null, 204);
    }
}
