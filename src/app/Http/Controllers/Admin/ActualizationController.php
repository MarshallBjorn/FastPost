<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Actualization;
use App\Models\Package;


class ActualizationController extends Controller
{
    public function index()
    {
        $actualizations = Actualization::with(['package', 'courier', 'lastWareHouse'])->latest()->get();
        return view('admin.actualizations.index', compact('actualizations'));
    }

    public function create()
    {
        $packages = Package::all();
        return view('admin.actualizations.create', compact('packages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'message' => 'required|string',
            'last_courier_id' => 'nullable|exists:users,id',
            'last_warehouse_id' => 'nullable|exists:warehouses,id',
            'created_at' => 'required|date',
        ]);

        Actualization::create($validated);
        return redirect()->route('actualizations.index')->with('success', 'Actualization created.');
    }

    public function show(Actualization $actualization)
    {
        return view('admin.actualizations.show', compact('actualization'));
    }

    public function edit(Actualization $actualization)
    {
        $packages = Package::all();
        return view('admin.actualizations.edit', compact('actualization', 'packages'));
    }

    public function update(Request $request, Actualization $actualization)
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'message' => 'required|string',
            'last_courier_id' => 'nullable|exists:users,id',
            'last_warehouse_id' => 'nullable|exists:warehouses,id',
            'created_at' => 'required|date',
        ]);

        $actualization->update($validated);
        return redirect()->route('actualizations.index')->with('success', 'Actualization updated.');
    }

    public function destroy(Actualization $actualization)
    {
        $actualization->delete();
        return redirect()->route('actualizations.index')->with('success', 'Actualization deleted.');
    }
}
