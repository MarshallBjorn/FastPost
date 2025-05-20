<?php

namespace App\Http\Controllers\Admin;

use App\Models\Warehouse;
use App\Models\Postmat;
use App\Models\WarehouseConnection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $warehouses = Warehouse::with('connectionsFrom.toWarehouse')->get();
        $postmats = Postmat::all();
        return view('admin.warehouses.index', compact('warehouses', 'postmats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.warehouses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'city' => 'required|string',
            'post_code' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'status' => 'required|in:active,unavailable,maintenance',
        ]);

        $warehouse = Warehouse::create($request->only([
            'city', 'post_code', 'latitude', 'longitude', 'status'
        ]));

        $this->syncConnections($warehouse, json_decode($request->input('connections'), true));

        return redirect()->route('warehouses.index')->with('success', 'Warehouse created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Warehouse $warehouse)
    {
        return view('admin.warehouses.show', compact('warehouse'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Warehouse $warehouse)
    {
        $all_warehouses = Warehouse::where('id', '!=', $warehouse->id)->get();

        // Get all connections where this warehouse is involved
        $connections = WarehouseConnection::where(function ($query) use ($warehouse) {
            $query->where('from_warehouse_id', $warehouse->id)
                ->orWhere('to_warehouse_id', $warehouse->id);
        })->get();
        $all_connections = WarehouseConnection::all(); 

        // Normalize connections to keys like "2-5"
        $connectedKeys = $connections->map(function ($conn) {
            return collect([$conn->from_warehouse_id, $conn->to_warehouse_id])->sort()->implode('-');
        });

        return view('admin.warehouses.edit', compact(
            'warehouse',
            'all_warehouses',
            'connectedKeys',
            'connections',
            'all_connections'
        ));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        $request->validate([
            'city' => 'required|string',
            'post_code' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'status' => 'required|in:active,unavailable,maintenance',
        ]);

        $warehouse->update($request->only([
            'city', 'post_code', 'latitude', 'longitude', 'status'
        ]));

        $connections = json_decode($request->input('connections'), true);

        $this->syncConnections($warehouse, $connections);

        return redirect()->route('warehouses.index')->with('success', 'Warehouse updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();
        return redirect()->route('warehouses.index')->with('success', 'Warehouse deleted.');
    }

    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    private function syncConnections(Warehouse $warehouse, array $submittedConnectionIds): void
    {
        $submittedKeys = collect($submittedConnectionIds);

        $existingConnections = WarehouseConnection::where(function ($query) use ($warehouse) {
            $query->where('from_warehouse_id', $warehouse->id)
                ->orWhere('to_warehouse_id', $warehouse->id);
        })->get();

        $existingKeys = $existingConnections->map(function ($conn) {
            return collect([$conn->from_warehouse_id, $conn->to_warehouse_id])->sort()->implode('-');
        });

        $toAdd = $submittedKeys->diff($existingKeys);
        $toRemove = $existingKeys->diff($submittedKeys);

        foreach ($toAdd as $key) {
            [$fromId, $toId] = explode('-', $key);
            $from = Warehouse::find($fromId);
            $to = Warehouse::find($toId);

            if (!$from || !$to) {
                Log::warning("Skipping invalid connection: $key");
                continue;
            }

            $distance = $this->haversineDistance($from->latitude, $from->longitude, $to->latitude, $to->longitude);

            WarehouseConnection::create([
                'from_warehouse_id' => $fromId,
                'to_warehouse_id' => $toId,
                'distance_km' => round($distance, 2),
            ]);
        }

        foreach ($toRemove as $key) {
            [$id1, $id2] = explode('-', $key);

            WarehouseConnection::where(function ($query) use ($id1, $id2) {
                $query->where(function ($q) use ($id1, $id2) {
                    $q->where('from_warehouse_id', $id1)->where('to_warehouse_id', $id2);
                })->orWhere(function ($q) use ($id1, $id2) {
                    $q->where('from_warehouse_id', $id2)->where('to_warehouse_id', $id1);
                });
            })->delete();
        }
    }

}
