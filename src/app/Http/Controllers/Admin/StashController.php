<?php

namespace App\Http\Controllers\Admin;

use App\Models\Stash;
use App\Models\Postmat;
use App\Models\Package;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StashController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Postmat $postmat)
    {
        // Assuming `stashes()` is a relationship on Postmat model
        $stashes = $postmat->stashes()->with('package')->get();
        

        return view('admin.stashes.index', compact('postmat', 'stashes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Postmat $postmat)
    {
        return view('admin.stashes.create', compact('postmat'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Postmat $postmat)
    {
        $validated = $request->validate([
            'size' => 'required|in:S,M,L',
        ]);

        $validated['postmat_id'] = $postmat->id;

        Stash::create($validated);

        return redirect()
            ->route('stashes.index', $postmat)
            ->with('success', 'Package successfully added to stash.');
    }


    /**
     * Display the specified resource.
     */
    public function show(Stash $stash)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Stash $stash)
    {
        $postmats = Postmat::all(); // Fetch all postmats for the dropdown
        return view('admin.stashes.edit', compact('stash', 'postmats'));
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Stash $stash)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Postmat $postmat, Stash $stash)
    {
        $stash->delete();
        return redirect()
            ->route('stashes.index', $postmat)->with('success', 'Stash deleted.');
    }
}
