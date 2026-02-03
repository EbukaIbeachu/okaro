<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Payments</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?php echo e(route('payments.create')); ?>" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg"></i> Record Payment
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="mb-3">
            <input type="text" id="paymentSearch" class="form-control" placeholder="Search payments...">
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle" id="paymentsTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Tenant</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($payment->payment_date); ?></td>
                        <td>
                            <a href="<?php echo e(route('tenants.show', $payment->rent->tenant)); ?>" class="text-decoration-none fw-bold">
                                <?php echo e($payment->rent->tenant->full_name); ?>

                            </a>
                            <br>
                            <small class="text-muted">Unit <?php echo e($payment->rent->unit->unit_number); ?></small>
                        </td>
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
                            <div class="btn-group">
                                <a href="<?php echo e(route('payments.show', $payment)); ?>" class="btn btn-sm btn-outline-secondary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?php echo e(route('payments.edit', $payment)); ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="<?php echo e(route('payments.destroy', $payment)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this payment?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-cash-stack fs-1 d-block mb-2"></i>
                            No payments found. Click "Record Payment" to add one.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            <?php echo e($payments->links()); ?>

        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setupTableSearch('paymentSearch', 'paymentsTable');
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Program Files\EasyPHP-Devserver-17\eds-www\okaro\resources\views/payments/index.blade.php ENDPATH**/ ?>