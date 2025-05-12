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

        if ($request->sort === 'name_asc') {
            $query->orderBy('name');
        } elseif ($request->sort === 'name_desc') {
            $query->orderByDesc('name');
        } elseif ($request->sort === 'city_asc') {
            $query->orderBy('city');
        } elseif ($request->sort === 'city_desc') {
            $query->orderByDesc('city');
        }

        $postmats = $query->paginate(9);

        return view('public.postmats.index', compact('postmats'));
    }
}
