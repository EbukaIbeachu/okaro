<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Maintenance Requests</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?php echo e(route('maintenance.create')); ?>" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg"></i> New Request
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="p-3 pb-0">
            <input type="text" id="maintenanceSearch" class="form-control" placeholder="Search requests...">
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="maintenanceTable">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Issue</th>
                        <th>Type</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Location</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($request->created_at->format('M d, Y')); ?></td>
                        <td>
                            <div class="fw-bold"><?php echo e(Str::limit($request->title, 40)); ?></div>
                            <div class="small text-muted"><?php echo e(Str::limit($request->description, 50)); ?></div>
                        </td>
                        <td><span class="badge bg-light text-dark border"><?php echo e($request->type); ?></span></td>
                        <td>
                            <?php if($request->priority === 'EMERGENCY'): ?>
                                <span class="badge bg-danger">EMERGENCY</span>
                            <?php elseif($request->priority === 'HIGH'): ?>
                                <span class="badge bg-warning text-dark">High</span>
                            <?php elseif($request->priority === 'MEDIUM'): ?>
                                <span class="badge bg-info text-dark">Medium</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Low</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($request->status === 'RESOLVED'): ?>
                                <span class="badge bg-success">Resolved</span>
                            <?php elseif($request->status === 'IN_PROGRESS'): ?>
                                <span class="badge bg-primary">In Progress</span>
                            <?php elseif($request->status === 'CANCELLED'): ?>
                                <span class="badge bg-secondary">Cancelled</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($request->unit): ?>
                                <div class="small fw-bold"><?php echo e($request->unit->building->name); ?></div>
                                <div class="small text-muted">Unit <?php echo e($request->unit->unit_number); ?></div>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo e(route('maintenance.show', $request)); ?>" class="btn btn-outline-secondary" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <?php if(auth()->user()->isAdmin() || auth()->user()->isManager() || $request->status === 'PENDING'): ?>
                                    <a href="<?php echo e(route('maintenance.edit', $request)); ?>" class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-tools fs-1 d-block mb-2"></i>
                            No maintenance requests found.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($requests->hasPages()): ?>
        <div class="card-footer bg-white">
            <?php echo e($requests->links()); ?>

        </div>
    <?php endif; ?>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setupTableSearch('maintenanceSearch', 'maintenanceTable');
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Program Files\EasyPHP-Devserver-17\eds-www\okaro\resources\views/maintenance/index.blade.php ENDPATH**/ ?>