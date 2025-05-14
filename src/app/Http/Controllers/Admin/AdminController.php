<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Stash;
use App\Models\Staff;
use App\Models\Actualization;
use Illuminate\support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        // 1. Package Status Distribution
        $packageCountsByStatus = Package::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');
    
        // 2. Packages Sent Per Day
        $packagesPerDay = Package::select(DB::raw('DATE(sent_at) as date'), DB::raw('count(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    
        // 3. Start Postmat Usage
        $startPostmatCounts = Package::select('start_postmat_id', DB::raw('count(*) as total'))
            ->groupBy('start_postmat_id')
            ->with('startPostmat:id,name')
            ->get()
            ->mapWithKeys(fn($p) => [$p->startPostmat->name ?? 'Unknown' => $p->total]);
    
        // 4. Destination Postmat Usage
        $destPostmatCounts = Package::select('destination_postmat_id', DB::raw('count(*) as total'))
            ->groupBy('destination_postmat_id')
            ->with('destinationPostmat:id,name')
            ->get()
            ->mapWithKeys(fn($p) => [$p->destinationPostmat->name ?? 'Unknown' => $p->total]);
    
        // 5. Staff Per Warehouse
        $staffByWarehouse = Staff::select('warehouse_id', DB::raw('count(*) as total'))
            ->groupBy('warehouse_id')
            ->with('warehouse:id,city')
            ->get()
            ->mapWithKeys(fn($s) => [$s->warehouse->city ?? 'Unknown' => $s->total]);
    
        // 6. Stashes per Postmat
        $stashesByPostmat = Stash::select('postmat_id', DB::raw('count(*) as total'))
            ->groupBy('postmat_id')
            ->with('postmat:id,name')
            ->get()
            ->mapWithKeys(fn($s) => [$s->postmat->name ?? 'Unknown' => $s->total]);
    
        // 7. Actualizations Per Day
        $actualizationsPerDay = Actualization::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    
        // 8. Packages Handled Per Courier
        $couriers = Actualization::select('last_courier_id', DB::raw('count(*) as total'))
            ->groupBy('last_courier_id')
            ->with('courier:id,first_name,last_name')
            ->get()
            ->mapWithKeys(fn($a) => [($a->courier->first_name ?? 'Unknown') . ' ' . ($a->courier->last_name ?? '') => $a->total]);
    
        return view('admin.dashboard', compact(
            'packageCountsByStatus',
            'packagesPerDay',
            'startPostmatCounts',
            'destPostmatCounts',
            'staffByWarehouse',
            'stashesByPostmat',
            'actualizationsPerDay',
            'couriers'
        ));
    }

    public function deliveries() {
        return view('admin.deliveries');
    }

    public function postmats() {
        return view('admin.postmats');
    }
}
