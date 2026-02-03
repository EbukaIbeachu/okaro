<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TenantController extends Controller
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
        $tenants = Tenant::with(['unit.building', 'user'])->paginate(15);
        return view('tenants.index', compact('tenants'));
    }

    public function create(Request $request)
    {
        // Only show units that are available or currently have no active tenant?
        // For simplicity, show all units, but maybe mark occupied ones.
        $units = Unit::with('building')->orderBy('building_id')->orderBy('unit_number')->get();
        $selectedUnitId = $request->query('unit_id');
        return view('tenants.create', compact('units', 'selectedUnitId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:150',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:50',
            'unit_id' => 'required|exists:units,id',
            'move_in_date' => 'required|date',
            'move_out_date' => 'nullable|date|after:move_in_date',
            'active' => 'boolean',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('tenants', 'public');
            $validated['profile_image'] = $path;
        }

        // Check for existing placeholder tenant (registered user without unit)
        $email = $validated['email'] ?? null;
        $tenantToUpdate = null;

        if ($email) {
            $tenantToUpdate = Tenant::where('email', $email)->whereNull('unit_id')->first();
        }

        if ($tenantToUpdate) {
            $tenantToUpdate->update($validated);
            $tenant = $tenantToUpdate;
            $message = 'Existing tenant profile updated with lease details.';
        } else {
            // Link to user if exists
            if ($email) {
                $existingUser = User::where('email', $email)->first();
                if ($existingUser) {
                    $validated['user_id'] = $existingUser->id;
                }
            }
            $tenant = Tenant::create($validated);
            $message = 'Tenant registered successfully.';
        }

        // Update unit status to OCCUPIED if tenant is active
        if ($tenant->active) {
            $unit = Unit::find($validated['unit_id']);
            if ($unit) {
                $unit->update(['status' => 'OCCUPIED']);
            }
        }

        return redirect()->route('tenants.index')->with('success', $message);
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['unit.building', 'rents', 'payments']);
        return view('tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant)
    {
        $units = Unit::with('building')->get();
        return view('tenants.edit', compact('tenant', 'units'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:150',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:50',
            'unit_id' => 'required|exists:units,id',
            'move_in_date' => 'required|date',
            'move_out_date' => 'nullable|date|after:move_in_date',
            'active' => 'boolean',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($tenant->profile_image) {
                Storage::disk('public')->delete($tenant->profile_image);
            }
            $path = $request->file('profile_image')->store('tenants', 'public');
            $validated['profile_image'] = $path;
        }

        $oldUnitId = $tenant->unit_id;
        $tenant->update($validated);

        // Handle Unit status changes
        if ($oldUnitId != $tenant->unit_id) {
            // Make old unit AVAILABLE (if no other active tenants)
            // Ideally we check if anyone else is there, but usually 1 unit = 1 tenant/family
            Unit::where('id', $oldUnitId)->update(['status' => 'AVAILABLE']);
            
            // Make new unit OCCUPIED
            if ($tenant->active) {
                Unit::where('id', $tenant->unit_id)->update(['status' => 'OCCUPIED']);
            }
        } elseif (!$tenant->active) {
             // If tenant marked inactive, free up the unit
             Unit::where('id', $tenant->unit_id)->update(['status' => 'AVAILABLE']);
        }

        return redirect()->route('tenants.index')->with('success', 'Tenant updated successfully.');
    }

    public function destroy(Tenant $tenant)
    {
        // Check for payments history
        if ($tenant->payments()->exists()) {
             return back()->with('error', 'Cannot delete tenant with payment history.');
        }

        $unitId = $tenant->unit_id;
        $tenant->delete();

        // Set unit to available
        Unit::where('id', $unitId)->update(['status' => 'AVAILABLE']);

        return redirect()->route('tenants.index')->with('success', 'Tenant deleted successfully.');
    }
}
