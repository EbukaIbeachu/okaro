<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Buildings</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?php echo e(route('buildings.create')); ?>" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg"></i> Add Building
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="mb-3">
            <input type="text" id="buildingSearch" class="form-control" placeholder="Search buildings...">
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle" id="buildingsTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Location</th>
                        <th class="text-center">Units</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $buildings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $building): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <a href="<?php echo e(route('buildings.show', $building)); ?>" class="text-decoration-none fw-bold">
                                <?php echo e($building->name); ?>

                            </a>
                        </td>
                        <td>
                            <?php echo e($building->address_line1); ?>

                            <?php if($building->address_line2): ?>
                                <br><small class="text-muted"><?php echo e($building->address_line2); ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php echo e($building->city); ?>, <?php echo e($building->state); ?> <?php echo e($building->postal_code); ?>

                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary"><?php echo e($building->units_count); ?> Total</span>
                            <?php if($building->active_units_count > 0): ?>
                                <span class="badge bg-success"><?php echo e($building->active_units_count); ?> Occupied</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <a href="<?php echo e(route('buildings.show', $building)); ?>" class="btn btn-sm btn-outline-secondary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?php echo e(route('buildings.edit', $building)); ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="<?php echo e(route('buildings.destroy', $building)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this building?');">
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
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="bi bi-building fs-1 d-block mb-2"></i>
                            No buildings found. Click "Add Building" to create one.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            <?php echo e($buildings->links()); ?>

        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setupTableSearch('buildingSearch', 'buildingsTable');
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Program Files\EasyPHP-Devserver-17\eds-www\okaro\resources\views/buildings/index.blade.php ENDPATH**/ ?>