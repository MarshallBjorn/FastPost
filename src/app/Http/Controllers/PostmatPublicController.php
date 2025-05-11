<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Postmat;

class PostmatPublicController extends Controller
{
    public function index(Request $request)
    {
        $postmats = Postmat::all();
        return view('public.postmats.index', compact('postmats'));
    }

    public function filter(Request $request)
    {
        $query = Postmat::query();

        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $query->orderBy($request->get('sort', 'name'), $request->get('direction', 'asc'));

        return response()->json($query->paginate(10));
    }
}
