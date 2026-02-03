<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Rental Agreements</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?php echo e(route('rents.create')); ?>" class="btn btn-sm btn-primary">
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
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $rents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <a href="<?php echo e(route('tenants.show', $rent->tenant)); ?>" class="text-decoration-none fw-bold">
                                <?php echo e($rent->tenant->full_name); ?>

                            </a>
                        </td>
                        <td>
                            <a href="<?php echo e(route('units.show', $rent->unit)); ?>" class="text-decoration-none text-dark">
                                <?php echo e($rent->unit->unit_number); ?>

                            </a>
                            <br>
                            <small class="text-muted"><?php echo e($rent->unit->building->name); ?></small>
                        </td>
                        <td>
                            <?php echo e($rent->start_date); ?> <br>
                            <small class="text-muted">to <?php echo e($rent->end_date ?? 'Indefinite'); ?></small>
                        </td>
                        <td>₦<?php echo e(number_format($rent->annual_amount, 2)); ?></td>
                        <td>
                            <?php if($rent->status === 'ACTIVE'): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-secondary"><?php echo e(ucfirst(strtolower($rent->status))); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <a href="<?php echo e(route('rents.agreement', $rent)); ?>" class="btn btn-sm btn-outline-dark" title="Download Agreement" target="_blank">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>
                                <a href="<?php echo e(route('rents.show', $rent)); ?>" class="btn btn-sm btn-outline-secondary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?php echo e(route('rents.edit', $rent)); ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="<?php echo e(route('rents.destroy', $rent)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this agreement?');">
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
                            <i class="bi bi-file-earmark-text fs-1 d-block mb-2"></i>
                            No rental agreements found. Click "Create Agreement" to add one.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            <?php echo e($rents->links()); ?>

        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setupTableSearch('rentSearch', 'rentsTable');
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Program Files\EasyPHP-Devserver-17\eds-www\okaro\resources\views/rents/index.blade.php ENDPATH**/ ?>