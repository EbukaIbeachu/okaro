<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Payment Details</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?php echo e(route('payments.index')); ?>" class="btn btn-sm btn-secondary me-2">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
        <a href="<?php echo e(route('payments.edit', $payment)); ?>" class="btn btn-sm btn-primary">
            <i class="bi bi-pencil"></i> Edit Payment
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">
                Transaction Summary
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <h2 class="display-4 fw-bold text-primary">₦<?php echo e(number_format($payment->amount, 2)); ?></h2>
                    <p class="text-muted"><?php echo e($payment->payment_date); ?></p>
                    
                    <?php if($payment->status === 'COMPLETED'): ?>
                        <span class="badge bg-success fs-6 px-3 py-2">Completed</span>
                    <?php elseif($payment->status === 'PENDING'): ?>
                        <span class="badge bg-warning text-dark fs-6 px-3 py-2">Pending</span>
                    <?php else: ?>
                        <span class="badge bg-danger fs-6 px-3 py-2">Failed</span>
                    <?php endif; ?>
                </div>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">Payment Method</span>
                        <span class="fw-bold"><?php echo e(ucfirst(str_replace('_', ' ', $payment->payment_method))); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">Transaction ID</span>
                        <span class="font-monospace small"><?php echo e($payment->id); ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-bold">
                Associated Lease
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Payer (Tenant)</h6>
                    <div class="d-flex align-items-center">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            <i class="bi bi-person"></i>
                        </div>
                        <div>
                            <a href="<?php echo e(route('tenants.show', $payment->rent->tenant)); ?>" class="text-decoration-none fw-bold">
                                <?php echo e($payment->rent->tenant->full_name); ?>

                            </a>
                            <div class="text-muted small"><?php echo e($payment->rent->tenant->email); ?></div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="text-muted mb-2">Property Unit</h6>
                    <div class="d-flex align-items-center">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            <i class="bi bi-house"></i>
                        </div>
                        <div>
                            <a href="<?php echo e(route('units.show', $payment->rent->unit)); ?>" class="text-decoration-none fw-bold">
                                Unit <?php echo e($payment->rent->unit->unit_number); ?>

                            </a>
                            <div class="text-muted small"><?php echo e($payment->rent->unit->building->name); ?></div>
                        </div>
                    </div>
                </div>

                <div>
                    <h6 class="text-muted mb-2">Lease Details</h6>
                    <div class="d-grid gap-2">
                        <a href="<?php echo e(route('rents.show', $payment->rent)); ?>" class="btn btn-outline-primary btn-sm">
                            View Lease Agreement
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Program Files\EasyPHP-Devserver-17\eds-www\okaro\resources\views/payments/show.blade.php ENDPATH**/ ?>