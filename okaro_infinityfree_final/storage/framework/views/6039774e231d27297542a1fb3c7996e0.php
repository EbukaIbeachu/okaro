<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Roles</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?php echo e(route('roles.create')); ?>" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg"></i> Add New Role
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Users Count</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e(ucfirst($role->name)); ?></td>
                        <td><?php echo e($role->description ?? '-'); ?></td>
                        <td>
                            <span class="badge bg-info rounded-pill"><?php echo e($role->users->count()); ?></span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="<?php echo e(route('roles.edit', $role)); ?>" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="<?php echo e(route('roles.destroy', $role)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure? This role cannot be deleted if it has assigned users.')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger" <?php echo e($role->users->count() > 0 ? 'disabled' : ''); ?>>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="text-center py-3">No roles found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Program Files\EasyPHP-Devserver-17\eds-www\okaro\resources\views/roles/index.blade.php ENDPATH**/ ?>