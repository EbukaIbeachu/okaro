<?php $__env->startSection('content'); ?>
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
                <?php if($rent): ?>
                    <h3 class="card-title"><?php echo e($rent->unit->building->name); ?></h3>
                    <p class="card-text text-muted mb-2">
                        <?php echo e($rent->unit->building->address); ?>

                    </p>
                    <p class="card-text text-muted mb-4">Unit <?php echo e($rent->unit->unit_number); ?></p>

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="small text-muted text-uppercase">Annual Rent</label>
                            <div class="fs-5 fw-bold">₦<?php echo e(number_format($rent->annual_amount, 2)); ?></div>
                        </div>
                        <div class="col-6">
                            <label class="small text-muted text-uppercase">Next Due Date</label>
                            <div class="fs-5"><?php echo e($rent->next_due_date ? $rent->next_due_date->format('M d, Y') : 'N/A'); ?></div>
                        </div>
                        <div class="col-6">
                            <label class="small text-muted text-uppercase">Lease Status</label>
                            <div><span class="badge bg-success">Active</span></div>
                        </div>
                        <div class="col-6">
                            <label class="small text-muted text-uppercase">Current Balance</label>
                            <div class="fs-5 <?php echo e($rent->balance > 0 ? 'text-danger' : 'text-success'); ?>">
                                ₦<?php echo e(number_format($rent->balance, 2)); ?>

                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <p>No active rental agreement found.</p>
                <?php endif; ?>
            </div>
            <?php if($rent && $rent->balance > 0): ?>
            <div class="card-footer">
                <a href="#" class="btn btn-primary w-100">Pay Now</a>
            </div>
            <?php endif; ?>
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
                            <?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($payment->payment_date->format('M d, Y')); ?></td>
                                <td class="text-success fw-bold">₦<?php echo e(number_format($payment->amount, 2)); ?></td>
                                <td><?php echo e($payment->due_date ? $payment->due_date->format('M d, Y') : 'N/A'); ?></td>
                                <td><?php echo e($payment->method ?? 'N/A'); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="text-center py-3">No payments recorded.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Program Files\EasyPHP-Devserver-17\eds-www\okaro\resources\views/tenant/dashboard.blade.php ENDPATH**/ ?>