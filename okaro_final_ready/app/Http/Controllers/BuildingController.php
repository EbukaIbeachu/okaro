<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Unit;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin() && !auth()->user()->isManager()) {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $buildings = Building::withCount(['units', 'activeUnits'])->paginate(10);
        return view('buildings.index', compact('buildings'));
    }

    public function create()
    {
        return view('buildings.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'address_line1' => 'required|string|max:200',
            'address_line2' => 'nullable|string|max:200',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'total_units' => 'required|integer|min:1',
            'total_floors' => 'required|integer|min:1',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('buildings', 'public');
            $validated['image_path'] = $imagePath;
        }

        $building = Building::create($validated);

        return redirect()->route('buildings.index')->with('success', 'Building created successfully.');
    }

    public function show(Building $building)
    {
        $building->load(['units.currentTenant', 'tenants', 'rents.payments', 'rents.tenant', 'rents.unit']);
        
        // Calculate stats
        $stats = [
            'total_units' => $building->units->count(),
            'occupied_units' => $building->units->where('status', 'OCCUPIED')->count(),
            'total_tenants' => $building->tenants->count(),
            'total_revenue' => $building->rents->flatMap->payments->sum('amount'),
        ];

        return view('buildings.show', compact('building', 'stats'));
    }

    public function edit(Building $building)
    {
        return view('buildings.edit', compact('building'));
    }

    public function update(Request $request, Building $building)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'address_line1' => 'required|string|max:200',
            'address_line2' => 'nullable|string|max:200',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'total_units' => 'required|integer|min:1',
            'total_floors' => 'required|integer|min:1',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('buildings', 'public');
            $validated['image_path'] = $imagePath;
        }

        $building->update($validated);

        return redirect()->route('buildings.index')->with('success', 'Building updated successfully.');
    }

    public function destroy(Building $building)
    {
        if ($building->units()->exists()) {
            return back()->with('error', 'Cannot delete building with existing units. Delete units first.');
        }

        $building->delete();

        return redirect()->route('buildings.index')->with('success', 'Building deleted successfully.');
    }
}
