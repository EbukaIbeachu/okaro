@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2" id="dashboard-title">{{ Auth::user()->isManager() ? 'Manager Dashboard' : 'Admin Dashboard' }}</h1>
    <button class="btn btn-outline-info btn-sm" onclick="startTour()">
        <i class="bi bi-question-circle"></i> Start Tour
    </button>
</div>

<!-- Stats Cards -->
<div class="row mb-4" id="stats-cards">
    <div class="col-md-3">
        <div class="card text-white bg-purple-light mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Total Buildings</h5>
                        <p class="card-text display-4">{{ $stats['total_buildings'] }}</p>
                    </div>
                    <i class="bi bi-building fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-purple-light mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Active Tenants</h5>
                        <p class="card-text display-4">{{ $stats['total_tenants'] }}</p>
                    </div>
                    <i class="bi bi-people-fill fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-purple-light mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">YTD Revenue</h5>
                        @php
                            $formattedRevenue = number_format($stats['paid_total'], 0);
                            $revLen = strlen($formattedRevenue);
                            // Base size display-5 (smaller than display-4), reduces as length increases
                            $revClass = 'display-5';
                            if ($revLen > 7) $revClass = 'display-6'; // > 999,999 (millions)
                            if ($revLen > 10) $revClass = 'fs-2';     // > 99,999,999 (hundred millions)
                        @endphp
                        <p class="card-text {{ $revClass }}">₦{{ $formattedRevenue }}</p>
                    </div>
                    <i class="bi bi-cash-stack fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-purple-light mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Overdue Rents</h5>
                        <p class="card-text display-4">{{ $stats['overdue_count'] }}</p>
                    </div>
                    <i class="bi bi-exclamation-triangle-fill fs-1"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4" id="revenue-section">
    <!-- Revenue Chart -->
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Revenue Overview (Last 6 Months)</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Payments -->
    <div class="col-md-6" id="recent-payments-section">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Recent Payments</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Tenant</th>
                                <th>Amount</th>
                                <th>Unit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPayments as $payment)
                            <tr>
                                <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                <td>{{ $payment->rent->tenant->full_name }}</td>
                                <td class="text-success">₦{{ number_format($payment->amount, 2) }}</td>
                                <td>{{ $payment->rent->unit->unit_number }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-3">No recent payments found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('payments.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
        </div>
    </div>

    <!-- Overdue Rents -->
    <div class="col-md-6" id="overdue-rents-section">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Overdue Rents</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tenant</th>
                                <th>Unit</th>
                                <th>Due Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($overdueRents as $rent)
                            <tr>
                                <td>{{ $rent->tenant->full_name }}</td>
                                <td>{{ $rent->unit->building->name }} - {{ $rent->unit->unit_number }}</td>
                                <td class="text-danger fw-bold">₦{{ number_format($rent->balance, 2) }}</td>
                                <td>
                                    <a href="{{ route('payments.create', ['rent_id' => $rent->id]) }}" class="btn btn-sm btn-primary">Pay</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-3">No overdue rents.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('rents.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('revenueChart').getContext('2d');
        var revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Revenue Collected (₦)',
                    data: @json($chartData),
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return '₦' + value;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
