<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Units</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?php echo e(route('units.create')); ?>" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg"></i> Add Unit
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>Unit</th>
                        <th>Building</th>
                        <th>Floor</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <a href="<?php echo e(route('units.show', $unit)); ?>" class="text-decoration-none fw-bold">
                                <?php echo e($unit->unit_number); ?>

                            </a>
                        </td>
                        <td>
                            <a href="<?php echo e(route('buildings.show', $unit->building)); ?>" class="text-decoration-none text-dark">
                                <?php echo e($unit->building->name); ?>

                            </a>
                        </td>
                        <td><?php echo e($unit->floor ?? '-'); ?></td>
                        <td><?php echo e($unit->bedrooms); ?> Bed / <?php echo e($unit->bathrooms); ?> Bath</td>
                        <td>
                            <?php if($unit->status === 'OCCUPIED'): ?>
                                <span class="badge bg-success">Occupied</span>
                            <?php elseif($unit->status === 'MAINTENANCE'): ?>
                                <span class="badge bg-warning text-dark">Maintenance</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Available</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <a href="<?php echo e(route('units.show', $unit)); ?>" class="btn btn-sm btn-outline-secondary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?php echo e(route('units.edit', $unit)); ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="<?php echo e(route('units.destroy', $unit)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this unit?');">
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
                            <i class="bi bi-door-closed fs-1 d-block mb-2"></i>
                            No units found. Click "Add Unit" to create one.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            <?php echo e($units->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Program Files\EasyPHP-Devserver-17\eds-www\okaro\resources\views/units/index.blade.php ENDPATH**/ ?>