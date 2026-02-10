@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Tenant Profile</h1>
    <div class="btn-toolbar mb-2 mb-md-0">

        <a href="{{ route('tenants.edit', $tenant) }}" class="btn btn-sm btn-primary">
            <i class="bi bi-pencil"></i> Edit Tenant
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-body text-center">
                @if($tenant->profile_image)
                    <img src="{{ asset('storage/' . $tenant->profile_image) }}" alt="{{ $tenant->full_name }}" class="rounded-circle object-fit-cover mx-auto mb-3" style="width: 120px; height: 120px;">
                @else
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-person fs-1"></i>
                    </div>
                @endif
                <h4 class="card-title">{{ $tenant->full_name }}</h4>
                @if(auth()->user()->isAdmin() && $tenant->creator)
                    <div class="mb-2 text-muted small">
                        <i class="bi bi-person-check me-1"></i> Registered by: {{ $tenant->creator->name }}
                    </div>
                @endif
                <p class="text-muted mb-2">
                    @if($tenant->active)
                        <span class="badge bg-success">Active Tenant</span>
                    @else
                        <span class="badge bg-secondary">Former Tenant</span>
                    @endif
                </p>
                
                <hr>
                
                <div class="text-start">
                    <p class="mb-1"><i class="bi bi-envelope me-2 text-muted"></i> {{ $tenant->email ?? 'N/A' }}</p>
                    <p class="mb-1"><i class="bi bi-telephone me-2 text-muted"></i> {{ $tenant->phone ?? 'N/A' }}</p>
                    <p class="mb-1"><i class="bi bi-calendar-event me-2 text-muted"></i> Since {{ $tenant->move_in_date }}</p>
                    @if($tenant->move_out_date)
                        <p class="mb-1"><i class="bi bi-calendar-x me-2 text-muted"></i> Left {{ $tenant->move_out_date }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8 mb-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">
                Current Residence
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="mb-1">{{ $tenant->unit->building->name }}</h5>
                        <p class="mb-0 text-muted">
                            Unit {{ $tenant->unit->unit_number }}<br>
                            {{ $tenant->unit->building->address_line1 }}, {{ $tenant->unit->building->city }}
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <a href="{{ route('units.show', $tenant->unit) }}" class="btn btn-outline-primary btn-sm">View Unit</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold">Lease Agreements</span>
                <a href="{{ route('rents.create', ['tenant_id' => $tenant->id]) }}" class="btn btn-sm btn-success">
                    <i class="bi bi-plus-lg"></i> New Lease
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Period</th>
                                <th>Amount</th>
                                <th>Due Day</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tenant->rents->sortByDesc('start_date') as $rent)
                            <tr>
                                <td>
                                    {{ $rent->start_date }} <span class="text-muted mx-1">to</span> {{ $rent->end_date }}
                                </td>
                                <td>₦{{ number_format($rent->annual_amount, 2) }}</td>
                                <td>{{ $rent->due_day }}th</td>
                                <td>
                                    @if($rent->status === 'ACTIVE')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst(strtolower($rent->status)) }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('rents.show', $rent) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">
                                    No lease agreements found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold">Payment History</span>
                <a href="{{ route('payments.create', ['tenant_id' => $tenant->id]) }}" class="btn btn-sm btn-success">
                    <i class="bi bi-plus-lg"></i> Record Payment
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tenant->payments->sortByDesc('payment_date')->take(5) as $payment)
                            <tr>
                                <td>{{ $payment->payment_date }}</td>
                                <td>₦{{ number_format($payment->amount, 2) }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                <td>
                                    @if($payment->status === 'COMPLETED')
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($payment->status === 'PENDING')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @else
                                        <span class="badge bg-danger">Failed</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">
                                    No payments recorded.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($tenant->payments->count() > 5)
                <div class="card-footer bg-white text-center">
                    <a href="{{ route('payments.index', ['tenant_id' => $tenant->id]) }}" class="text-decoration-none">View All Payments</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
