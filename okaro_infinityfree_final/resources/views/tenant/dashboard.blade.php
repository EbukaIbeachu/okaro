@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">My Dashboard</h1>
</div>

<div class="row">
    <!-- Current Lease Info -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">My Home</h5>
            </div>
            <div class="card-body">
                @if($rent)
                    <h3 class="card-title">{{ $rent->unit->building->name }}</h3>
                    <p class="card-text text-muted mb-2">
                        {{ $rent->unit->building->address }}
                    </p>
                    <p class="card-text text-muted mb-4">Unit {{ $rent->unit->unit_number }}</p>

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="small text-muted text-uppercase">Annual Rent</label>
                            <div class="fs-5 fw-bold">₦{{ number_format($rent->annual_amount, 2) }}</div>
                        </div>
                        <div class="col-6">
                            <label class="small text-muted text-uppercase">Next Due Date</label>
                            <div class="fs-5">{{ $rent->next_due_date ? $rent->next_due_date->format('M d, Y') : 'N/A' }}</div>
                        </div>
                        <div class="col-6">
                            <label class="small text-muted text-uppercase">Lease Status</label>
                            <div><span class="badge bg-success">Active</span></div>
                        </div>
                        <div class="col-6">
                            <label class="small text-muted text-uppercase">Current Balance</label>
                            <div class="fs-5 {{ $rent->balance > 0 ? 'text-danger' : 'text-success' }}">
                                ₦{{ number_format($rent->balance, 2) }}
                            </div>
                        </div>
                    </div>
                @else
                    <p>No active rental agreement found.</p>
                @endif
            </div>
            @if($rent && $rent->balance > 0)
            <div class="card-footer">
                <a href="#" class="btn btn-primary w-100">Pay Now</a>
            </div>
            @endif
        </div>
    </div>

    <!-- Recent Payments -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-light">
                <h5 class="mb-0">Payment History</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Due Date</th>
                                <th>Method</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                            <tr>
                                <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                <td class="text-success fw-bold">₦{{ number_format($payment->amount, 2) }}</td>
                                <td>{{ $payment->due_date ? $payment->due_date->format('M d, Y') : 'N/A' }}</td>
                                <td>{{ $payment->method ?? 'N/A' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-3">No payments recorded.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
