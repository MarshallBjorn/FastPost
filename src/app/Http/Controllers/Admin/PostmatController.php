<?php

namespace App\Http\Controllers\Admin;

use App\Models\Postmat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Validated;

class PostmatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $postmats = Postmat::with(["stashes.package"])->paginate(10);
        return view('admin.postmats.index', compact('postmats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $postmats = Postmat::all();
        return view('admin.postmats.create', compact('postmats'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'city' => 'required|string',
            'post_code' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'status' => 'required|in:active,unavailable,maintenance',
        ]);

        Postmat::create($validated);
        return redirect()->route('postmats.index')->with('success', 'Postmat created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Postmat $postmat)
    {
        return redirect()->route('stashes.index', $postmat);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Postmat $postmat)
    {
        return view('admin.postmats.edit', compact('postmat'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Postmat $postmat)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'city' => 'required|string',
            'post_code' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'status' => 'required|in:active,unavailable,maintenance',
        ]);

        $postmat->update($validated);
        return redirect()->route('postmats.index')->with('success', 'Postmat updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Postmat $postmat)
    {
        $postmat->delete();
        return redirect()->route('postmats.index')->with('success', 'Postmat deleted.');
    }
}
