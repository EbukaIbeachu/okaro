@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Create Rental Agreement</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('rents.index') }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Agreement Details</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('rents.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tenant_id" class="form-label">Tenant <span class="text-danger">*</span></label>
                            <select class="form-select @error('tenant_id') is-invalid @enderror" id="tenant_id" name="tenant_id" required>
                                <option value="">Select Tenant</option>
                                @foreach($tenants as $tenant)
                                    <option value="{{ $tenant->id }}" {{ (old('tenant_id') == $tenant->id || (isset($selectedTenantId) && $selectedTenantId == $tenant->id)) ? 'selected' : '' }}>
                                        {{ $tenant->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tenant_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="unit_id" class="form-label">Unit <span class="text-danger">*</span></label>
                            <select class="form-select @error('unit_id') is-invalid @enderror" id="unit_id" name="unit_id" required>
                                <option value="">Select Unit</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ (old('unit_id') == $unit->id || (isset($selectedUnitId) && $selectedUnitId == $unit->id)) ? 'selected' : '' }}>
                                        {{ $unit->building->name }} - Unit {{ $unit->unit_number }}
                                    </option>
                                @endforeach
                            </select>
                            @error('unit_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="annual_amount" class="form-label">Annual Rent (₦) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₦</span>
                                <input type="number" step="0.01" class="form-control @error('annual_amount') is-invalid @enderror" id="annual_amount" name="annual_amount" value="{{ old('annual_amount') }}" required min="0">
                            </div>
                            @error('annual_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="due_day" class="form-label">Due Day (1-31) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('due_day') is-invalid @enderror" id="due_day" name="due_day" value="{{ old('due_day', 1) }}" required min="1" max="31">
                            <div class="form-text">Day of the month when rent is due. If an End Date is set, this will be automatically updated to match it.</div>
                            @error('due_day')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date') }}">
                            <div class="form-text">Leave blank for indefinite/month-to-month.</div>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <button type="reset" class="btn btn-light me-md-2">Reset</button>
                        <button type="submit" class="btn btn-primary">Create Agreement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Building Filter Logic
        const buildingSelect = document.getElementById('building_filter');
        const unitSelect = document.getElementById('unit_id');
        const unitOptions = Array.from(unitSelect.options);

        function filterUnits(resetValue = true) {
            const selectedBuildingId = buildingSelect.value;
            
            if (resetValue) {
                unitSelect.value = "";
            }
            
            unitOptions.forEach(option => {
                if (option.value === "") return; // Skip placeholder
                
                const buildingId = option.getAttribute('data-building-id');
                if (!selectedBuildingId || buildingId === selectedBuildingId) {
                    option.style.display = "";
                    option.disabled = false;
                } else {
                    option.style.display = "none";
                    option.disabled = true;
                }
            });
        }

        buildingSelect.addEventListener('change', function() {
            filterUnits(true);
        });

        // Initialize state (e.g. validation errors or edit mode)
        if (unitSelect.value) {
            const selectedOption = unitSelect.options[unitSelect.selectedIndex];
            const buildingId = selectedOption.getAttribute('data-building-id');
            if (buildingId) {
                buildingSelect.value = buildingId;
            }
            // Filter but don't reset the selected value
            filterUnits(false);
        }

        // Date Logic
        const endDateInput = document.getElementById('end_date');
        const dueDayInput = document.getElementById('due_day');

        endDateInput.addEventListener('change', function() {
            if (this.value) {
                const date = new Date(this.value);
                if (!isNaN(date.getTime())) {
                    dueDayInput.value = date.getDate();
                }
            }
        });
    });
</script>
@endpush
