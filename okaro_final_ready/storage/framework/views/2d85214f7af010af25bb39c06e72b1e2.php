<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Rental Agreement Details</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?php echo e(route('rents.index')); ?>" class="btn btn-sm btn-secondary me-2">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
        <a href="<?php echo e(route('rents.edit', $rent)); ?>" class="btn btn-sm btn-primary">
            <i class="bi bi-pencil"></i> Edit Agreement
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-bold">
                Agreement Summary
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <th scope="row" class="text-muted" style="width: 40%">Status</th>
                            <td>
                                <?php if($rent->status === 'ACTIVE'): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?php echo e(ucfirst(strtolower($rent->status))); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-muted">Annual Rent</th>
                            <td class="fs-5 fw-bold">₦<?php echo e(number_format($rent->annual_amount, 2)); ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-muted">Due Day</th>
                            <td><?php echo e($rent->due_day); ?>th of every month</td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-muted">Lease Period</th>
                            <td>
                                <?php echo e($rent->start_date); ?> 
                                <i class="bi bi-arrow-right mx-1 text-muted"></i>
                                <?php echo e($rent->end_date ?? 'Indefinite'); ?>

                            </td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-muted">Signed Agreement</th>
                            <td>
                                <?php if($rent->signed_agreement_path): ?>
                                    <a href="<?php echo e(Storage::url($rent->signed_agreement_path)); ?>" target="_blank" class="btn btn-sm btn-outline-primary mb-2">
                                        <i class="bi bi-file-earmark-pdf"></i> View Signed Copy
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted fst-italic">Not uploaded</span>
                                <?php endif; ?>

                                <form action="<?php echo e(route('rents.upload-agreement', $rent)); ?>" method="POST" enctype="multipart/form-data" class="mt-2">
                                    <?php echo csrf_field(); ?>
                                    <div class="input-group input-group-sm">
                                        <input type="file" class="form-control" name="signed_agreement" accept=".pdf,.jpg,.jpeg,.png" required>
                                        <button class="btn btn-primary" type="submit">Upload</button>
                                    </div>
                                    <div class="form-text small">Max 10MB (PDF/Image)</div>
                                </form>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-bold">
                Parties Involved
            </div>
            <div class="card-body">
                <h6 class="text-muted mb-2">Tenant</h6>
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="bi bi-person"></i>
                    </div>
                    <div>
                        <a href="<?php echo e(route('tenants.show', $rent->tenant)); ?>" class="text-decoration-none fw-bold">
                            <?php echo e($rent->tenant->full_name); ?>

                        </a>
                        <div class="text-muted small"><?php echo e($rent->tenant->email); ?></div>
                    </div>
                </div>

                <hr class="my-3">

                <h6 class="text-muted mb-2">Property</h6>
                <div class="d-flex align-items-center">
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="bi bi-house"></i>
                    </div>
                    <div>
                        <a href="<?php echo e(route('units.show', $rent->unit)); ?>" class="text-decoration-none fw-bold">
                            Unit <?php echo e($rent->unit->unit_number); ?>

                        </a>
                        <div class="text-muted small"><?php echo e($rent->unit->building->name); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-bold">Payment History</span>
        <a href="<?php echo e(route('payments.create', ['rent_id' => $rent->id])); ?>" class="btn btn-sm btn-success">
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
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $rent->payments->sortByDesc('payment_date'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($payment->payment_date); ?></td>
                        <td>₦<?php echo e(number_format($payment->amount, 2)); ?></td>
                        <td><?php echo e(ucfirst(str_replace('_', ' ', $payment->payment_method))); ?></td>
                        <td>
                            <?php if($payment->status === 'COMPLETED'): ?>
                                <span class="badge bg-success">Completed</span>
                            <?php elseif($payment->status === 'PENDING'): ?>
                                <span class="badge bg-warning text-dark">Pending</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Failed</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <a href="<?php echo e(route('payments.show', $payment)); ?>" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="text-center py-3 text-muted">
                            No payments recorded for this agreement.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Program Files\EasyPHP-Devserver-17\eds-www\okaro\resources\views/rents/show.blade.php ENDPATH**/ ?>