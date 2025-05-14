<?php

namespace App\Http\Controllers\Admin;

use App\Models\Stash;
use App\Models\Postmat;
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
        $stashes = $postmat->stashes()->with('packages')->get();
        

        return view('admin.stashes.index', compact('postmat', 'stashes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
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
    public function destroy(Stash $stash)
    {
        //
    }
}
