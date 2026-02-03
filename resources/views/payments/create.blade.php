@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Record Payment</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('payments.index') }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Payment Details</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('payments.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="rent_id" class="form-label">Lease Agreement <span class="text-danger">*</span></label>
                        <input type="text" id="lease_search" class="form-control mb-2" placeholder="Type to filter leases...">
                        <select class="form-select @error('rent_id') is-invalid @enderror" id="rent_id" name="rent_id" required>
                            <option value="">Select Lease</option>
                            @foreach($rents as $rent)
                                <option value="{{ $rent->id }}" 
                                        data-balance="{{ $rent->balance }}"
                                        {{ (old('rent_id') == $rent->id || (isset($selectedRent) && $selectedRent && $selectedRent->id == $rent->id)) ? 'selected' : '' }}>
                                    {{ $rent->tenant->full_name }} - Unit {{ $rent->unit->unit_number }} (₦{{ number_format($rent->annual_amount, 2) }}/yr)
                                </option>
                            @endforeach
                        </select>
                        <div id="outstanding-alert" class="alert mt-2 d-none">
                            <strong>Outstanding Due:</strong> ₦<span id="outstanding-amount">0.00</span>
                        </div>
                        @error('rent_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="amount" class="form-label">Amount Paid (₦) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₦</span>
                                <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount') }}" required min="0.01">
                            </div>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('payment_date') is-invalid @enderror" id="payment_date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                            @error('payment_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method">
                                <option value="CASH" {{ old('payment_method') == 'CASH' ? 'selected' : '' }}>Cash</option>
                                <option value="BANK_TRANSFER" {{ old('payment_method') == 'BANK_TRANSFER' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="CHECK" {{ old('payment_method') == 'CHECK' ? 'selected' : '' }}>Check</option>
                                <option value="ONLINE" {{ old('payment_method') == 'ONLINE' ? 'selected' : '' }}>Online</option>
                                <option value="OTHER" {{ old('payment_method') == 'OTHER' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <button type="reset" class="btn btn-light me-md-2">Reset</button>
                        <button type="submit" class="btn btn-primary">Record Payment</button>
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
        const rentSelect = document.getElementById('rent_id');
        const alertBox = document.getElementById('outstanding-alert');
        const amountSpan = document.getElementById('outstanding-amount');

        function updateOutstanding() {
            const selectedOption = rentSelect.options[rentSelect.selectedIndex];
            if (!selectedOption || !selectedOption.value) {
                alertBox.classList.add('d-none');
                return;
            }

            const balance = parseFloat(selectedOption.getAttribute('data-balance'));
            
            if (!isNaN(balance)) {
                alertBox.classList.remove('d-none');
                alertBox.classList.remove('alert-success', 'alert-warning', 'alert-danger');

                if (balance > 0) {
                    amountSpan.textContent = balance.toLocaleString('en-NG', {minimumFractionDigits: 2});
                    alertBox.classList.add('alert-warning');
                    alertBox.querySelector('strong').textContent = 'Outstanding Due:';
                } else if (balance < 0) {
                    amountSpan.textContent = Math.abs(balance).toLocaleString('en-NG', {minimumFractionDigits: 2});
                    alertBox.classList.add('alert-success');
                    alertBox.querySelector('strong').textContent = 'Credit Balance:';
                } else {
                    amountSpan.textContent = '0.00';
                    alertBox.classList.add('alert-success');
                    alertBox.querySelector('strong').textContent = 'All Caught Up:';
                }
            }
        }

        rentSelect.addEventListener('change', updateOutstanding);
        
        if (rentSelect.value) {
            updateOutstanding();
        }

        // Search Filter Logic
        const leaseSearch = document.getElementById('lease_search');
        const leaseOptions = Array.from(rentSelect.options);

        leaseSearch.addEventListener('input', function() {
            const searchText = this.value.toLowerCase();
            
            leaseOptions.forEach(option => {
                if (option.value === "") return;
                
                const text = option.text.toLowerCase();
                if (text.includes(searchText)) {
                    option.hidden = false;
                    option.disabled = false;
                    option.style.display = "";
                } else {
                    option.hidden = true;
                    option.disabled = true;
                    option.style.display = "none";
                }
            });
            
            // Check if selected value is now hidden
            const selectedOption = rentSelect.options[rentSelect.selectedIndex];
            if (selectedOption && selectedOption.hidden) {
                rentSelect.value = "";
                updateOutstanding();
            }
        });
    });
</script>
@endpush
