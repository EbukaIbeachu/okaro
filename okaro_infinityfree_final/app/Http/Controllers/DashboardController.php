<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Building;
use App\Models\Tenant;
use App\Models\Rent;
use App\Models\Payment;
use App\Models\Unit;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin() || $user->isManager()) {
            return $this->adminDashboard();
        }

        if ($user->isTenant()) {
            return $this->tenantDashboard();
        }

        return redirect()->route('login');
    }

    private function adminDashboard()
    {
        // Statistics
        $stats = [
            'total_buildings' => Building::count(),
            'total_tenants' => Tenant::where('active', true)->count(),
            'paid_total' => Payment::where('status', 'COMPLETED')->whereYear('payment_date', now()->year)->sum('amount'),
            'overdue_count' => Rent::active()->get()->filter(function ($rent) {
                return $rent->balance > 0;
            })->count(),
        ];

        // Monthly Revenue (Last 6 months)
        $monthlyRevenue = Payment::where('status', 'COMPLETED')
            ->where('payment_date', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw('DATE_FORMAT(payment_date, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->all();
        
        // Fill missing months with 0
        $chartData = [];
        $chartLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            $chartLabels[] = $date->format('M Y');
            $chartData[] = $monthlyRevenue[$monthKey] ?? 0;
        }

        // Recent Payments
        $recentPayments = Payment::with(['rent.tenant', 'rent.unit.building'])
            ->latest('payment_date')
            ->take(10)
            ->get();

        // Overdue Rents (Simplified logic for example)
        $overdueRents = Rent::active()
            ->with(['tenant', 'unit.building'])
            ->get()
            ->filter(function ($rent) {
                return $rent->balance > 0;
            });

        // Pending Requests (Placeholder)
        $requests = []; 

        return view('admin.dashboard', compact('stats', 'recentPayments', 'overdueRents', 'requests', 'chartData', 'chartLabels'));
    }

    private function tenantDashboard()
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        
        if (!$tenant) {
            // Auto-create tenant profile if missing for tenant user
            if ($user->isTenant()) {
                 // Check for existing tenant profile by email first
                 $existingTenant = Tenant::where('email', $user->email)->first();
                 
                 if ($existingTenant) {
                     $existingTenant->update(['user_id' => $user->id]);
                     $tenant = $existingTenant;
                 } else {
                     $tenant = Tenant::create([
                        'user_id' => $user->id,
                        'full_name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'active' => true,
                    ]);
                 }
            } else {
                abort(403, 'Tenant profile not found.');
            }
        }

        $rent = $tenant->currentRent;
        $payments = $tenant->payments()->latest('payment_date')->take(5)->get();
        
        return view('tenant.dashboard', compact('tenant', 'rent', 'payments'));
    }
}
