<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Actualization;
use App\Models\Package;


class ActualizationController extends Controller
{
    public function index(Request $request)
    {
        $query = Actualization::with(['package', 'courier', 'currentWarehouse'])->latest();
        
        if ($request->filled('id')) {
            $query->where('package_id', $request->input('id')); // exact match is correct here
        }

        $actualizations = $query->paginate(10)->withQueryString();
        return view('admin.actualizations.index', compact('actualizations'));
    }
    
    public function edit(Actualization $actualization)
    {
        $packages = Package::all();
        $warehouses = \App\Models\Warehouse::all();
        $couriers = \App\Models\User::all(); // filter if needed
        return view('admin.actualizations.edit', compact('actualization', 'packages', 'warehouses', 'couriers'));
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

    public function show(Actualization $actualization)
    {
        // Eager load relations if needed
        $actualization->load(['package', 'courier', 'currentWarehouse', 'nextWarehouse']);

        return view('admin.actualizations.show', compact('actualization'));
    }

}
