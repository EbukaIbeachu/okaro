@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2" id="rents-header">Rental Agreements</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button class="btn btn-sm btn-outline-info me-2" onclick="startTour()">
            <i class="bi bi-question-circle"></i> Help
        </button>
        <a href="{{ route('rents.create') }}" class="btn btn-sm btn-primary" id="add-rent-btn">
            <i class="bi bi-plus-lg"></i> Create Agreement
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="mb-3">
            <input type="text" id="rentSearch" class="form-control" placeholder="Search agreements...">
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle" id="rentsTable">
                <thead>
                    <tr>
                        <th>Tenant</th>
                        <th>Unit</th>
                        <th>Period</th>
                        <th>Amount</th>
                        @if(Auth::user()->isAdmin())
                        <th>Created By</th>
                        @endif
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rents as $rent)
                    <tr>
                        <td>
                            <a href="{{ route('tenants.show', $rent->tenant) }}" class="text-decoration-none fw-bold">
                                {{ $rent->tenant->full_name }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('units.show', $rent->unit) }}" class="text-decoration-none text-dark">
                                {{ $rent->unit->unit_number }}
                            </a>
                            <br>
                            <small class="text-muted">{{ $rent->unit->building->name }}</small>
                        </td>
                        <td>
                            {{ $rent->start_date }} <br>
                            <small class="text-muted">to {{ $rent->end_date ?? 'Indefinite' }}</small>
                        </td>
                        <td>₦{{ number_format($rent->annual_amount, 2) }}</td>
                        @if(Auth::user()->isAdmin())
                        <td>
                            @if($rent->creator)
                                <div class="d-flex align-items-center">
                                    <div class="ms-2">
                                        <div class="fw-bold small">{{ $rent->creator->name }}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">{{ $rent->creator->role->name ?? 'User' }}</div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted small fst-italic">System</span>
                            @endif
                        </td>
                        @endif
                        <td>
                            @if($rent->status === 'ACTIVE')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst(strtolower($rent->status)) }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <a href="{{ route('rents.agreement', $rent) }}" class="btn btn-sm btn-outline-dark" title="Download Agreement" target="_blank">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>
                                <a href="{{ route('rents.show', $rent) }}" class="btn btn-sm btn-outline-secondary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('rents.edit', $rent) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('rents.destroy', $rent) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this agreement?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-file-earmark-text fs-1 d-block mb-2"></i>
                            No rental agreements found. Click "Create Agreement" to add one.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $rents->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setupTableSearch('rentSearch', 'rentsTable');
    });
</script>
@endpush
@endsection
