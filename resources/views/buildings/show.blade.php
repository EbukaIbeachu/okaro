@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="d-flex align-items-center">
                            @if($building->image_path)
                                <img src="{{ Storage::url($building->image_path) }}" alt="{{ $building->name }}" class="rounded me-3 object-fit-cover" style="width: 100px; height: 100px;">
                            @else
                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center text-muted" style="width: 100px; height: 100px;">
                                    <i class="bi bi-building fs-1"></i>
                                </div>
                            @endif
                            <div>
                                <h1 class="h2 mb-1">{{ $building->name }}</h1>
                                <p class="text-muted mb-2">
                                    <i class="bi bi-geo-alt me-1"></i> {{ $building->address }}
                                </p>
                                @if(auth()->user()->isAdmin() && $building->creator)
                                    <div class="mb-2 text-muted small">
                                        <i class="bi bi-person-check me-1"></i> Registered by: {{ $building->creator->name }}
                                    </div>
                                @endif
                                <div class="d-flex gap-2">
                                    <span class="badge bg-primary">{{ $stats['total_units'] }} Units</span>
                                    <span class="badge bg-success">{{ $stats['occupied_units'] }} Occupied</span>
                                    <span class="badge bg-info text-dark">{{ $stats['total_tenants'] }} Tenants</span>
                                </div>
                            </div>
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('buildings.edit', $building) }}" class="btn btn-outline-primary">
                                <i class="bi bi-pencil"></i> Edit Details
                            </a>
                            <a href="{{ route('buildings.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small fw-bold">Total Revenue</h6>
                    <h3 class="mb-0">₦{{ number_format($stats['total_revenue'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-success">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small fw-bold">Occupancy Rate</h6>
                    <h3 class="mb-0">
                        {{ $stats['total_units'] > 0 ? round(($stats['occupied_units'] / $stats['total_units']) * 100) : 0 }}%
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-warning">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small fw-bold">Maintenance</h6>
                    <h3 class="mb-0">{{ $stats['maintenance_units'] }} Units</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-info">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small fw-bold">Available</h6>
                    <h3 class="mb-0">{{ $stats['available_units'] }} Units</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Tabs -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom">
            <ul class="nav nav-tabs card-header-tabs" id="buildingTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="units-tab" data-bs-toggle="tab" data-bs-target="#units" type="button" role="tab">
                        <i class="bi bi-door-closed me-1"></i> Units
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tenants-tab" data-bs-toggle="tab" data-bs-target="#tenants" type="button" role="tab">
                        <i class="bi bi-people me-1"></i> Tenants
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="rents-tab" data-bs-toggle="tab" data-bs-target="#rents" type="button" role="tab">
                        <i class="bi bi-file-text me-1"></i> Leases & Agreements
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab">
                        <i class="bi bi-cash-stack me-1"></i> Financials
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="buildingTabsContent">
                
                <!-- Units Tab -->
                <div class="tab-pane fade show active" id="units" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Unit Directory</h5>
                        <a href="{{ route('units.create', ['building_id' => $building->id]) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-lg"></i> Add New Unit
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Unit #</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Current Tenant</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($building->units as $unit)
                                <tr>
                                    <td class="fw-bold">{{ $unit->unit_number }}</td>
                                    <td>
                                        <div class="small">{{ $unit->bedrooms }} Bed, {{ $unit->bathrooms }} Bath</div>
                                        <div class="text-muted small">Floor {{ $unit->floor ?? 'G' }}</div>
                                    </td>
                                    <td>
                                        @if($unit->status === 'OCCUPIED')
                                            <span class="badge bg-success">Occupied</span>
                                        @elseif($unit->status === 'MAINTENANCE')
                                            <span class="badge bg-warning text-dark">Maintenance</span>
                                        @else
                                            <span class="badge bg-secondary">Available</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $tenant = $unit->currentTenant;
                                            if (!$tenant) {
                                                // Fallback: check building's loaded rents for this unit
                                                $activeRent = $building->rents->where('unit_id', $unit->id)->where('status', 'ACTIVE')->first();
                                                if ($activeRent) {
                                                    $tenant = $activeRent->tenant;
                                                }
                                            }
                                        @endphp
                                        @if($tenant)
                                            <a href="{{ route('tenants.show', $tenant) }}" class="text-decoration-none fw-bold">
                                                {{ $tenant->full_name }}
                                            </a>
                                        @else
                                            <span class="text-muted fst-italic">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('units.edit', $unit) }}" class="btn btn-outline-secondary" title="Edit Unit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('units.destroy', $unit) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No units found in this building.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tenants Tab -->
                <div class="tab-pane fade" id="tenants" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Active Tenants</h5>
                        <a href="{{ route('tenants.create') }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-lg"></i> Register Tenant
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Unit</th>
                                    <th>Contact</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($building->tenants->unique('id') as $tenant)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $tenant->full_name }}</div>
                                        <div class="small text-muted">{{ $tenant->email }}</div>
                                    </td>
                                    <td>
                                        <!-- Find tenant's unit in this building -->
                                        @php
                                            $tenantUnit = $building->units->first(function($u) use ($tenant) {
                                                return $u->currentTenant && $u->currentTenant->id === $tenant->id;
                                            });
                                        @endphp
                                        <span class="badge bg-light text-dark border">
                                            {{ $tenantUnit ? $tenantUnit->unit_number : 'History' }}
                                        </span>
                                    </td>
                                    <td>{{ $tenant->phone_number }}</td>
                                    <td>
                                        @if($tenant->active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('tenants.show', $tenant) }}" class="btn btn-sm btn-outline-primary">
                                            View Profile
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No tenants found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Leases Tab -->
                <div class="tab-pane fade" id="rents" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Rental Agreements</h5>
                        <a href="{{ route('rents.create') }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-lg"></i> Create Lease
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Tenant</th>
                                    <th>Unit</th>
                                    <th>Duration</th>
                                    <th>Amount (Yearly)</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($building->rents->sortByDesc('start_date') as $rent)
                                <tr>
                                    <td>{{ $rent->tenant->full_name }}</td>
                                    <td>{{ $rent->unit->unit_number }}</td>
                                    <td>
                                        <div class="small">{{ $rent->start_date->format('M d, Y') }}</div>
                                        <div class="small text-muted">to {{ $rent->end_date ? $rent->end_date->format('M d, Y') : 'Indefinite' }}</div>
                                    </td>
                                    <td class="fw-bold">₦{{ number_format($rent->annual_amount, 2) }}</td>
                                    <td>
                                        @if($rent->status === 'ACTIVE')
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst(strtolower($rent->status)) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('rents.show', $rent) }}" class="btn btn-outline-primary" title="Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('rents.agreement', $rent) }}" class="btn btn-outline-dark" title="PDF Agreement">
                                                <i class="bi bi-file-pdf"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No agreements found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Financials Tab -->
                <div class="tab-pane fade" id="payments" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Payment History</h5>
                        <a href="{{ route('payments.create') }}" class="btn btn-sm btn-success">
                            <i class="bi bi-plus-lg"></i> Record Payment
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Tenant</th>
                                    <th>Reference</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $payments = $building->rents->flatMap->payments->sortByDesc('payment_date');
                                @endphp
                                @forelse($payments as $payment)
                                <tr>
                                    <td>{{ $payment->payment_date }}</td>
                                    <td>{{ $payment->rent->tenant->full_name }}</td>
                                    <td class="font-monospace small">{{ Str::limit($payment->rent->unit->unit_number . '-' . $payment->id, 15) }}</td>
                                    <td class="fw-bold text-success">+₦{{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ ucfirst($payment->payment_method) }}</td>
                                    <td>
                                        <span class="badge bg-success">Completed</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No payments recorded yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection