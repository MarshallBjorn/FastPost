<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Postmat;

class PostmatPublicController extends Controller
{
    public function index(Request $request)
    {
        $query = Postmat::query();

        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Clone query for pagination
        $paginatedPostmats = (clone $query);

        // Sorting
        if ($request->sort === 'name_asc') {
            $query->orderBy('name');
            $paginatedPostmats->orderBy('name');
        } elseif ($request->sort === 'name_desc') {
            $query->orderByDesc('name');
            $paginatedPostmats->orderByDesc('name');
        } elseif ($request->sort === 'city_asc') {
            $query->orderBy('city');
            $paginatedPostmats->orderBy('city');
        } elseif ($request->sort === 'city_desc') {
            $query->orderByDesc('city');
            $paginatedPostmats->orderByDesc('city');
        }

        // For map: all matching postmats (unpaginated)
        $allPostmats = $query->get();

        // For grid: paginated
        $postmats = $paginatedPostmats->paginate(9);

        return view('public.postmats.index', compact('postmats', 'allPostmats'));
    }
}
