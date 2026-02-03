<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Request Details</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?php echo e(route('maintenance.index')); ?>" class="btn btn-sm btn-secondary me-2">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <?php if(auth()->user()->isAdmin() || auth()->user()->isManager() || $maintenance->status === 'PENDING'): ?>
            <a href="<?php echo e(route('maintenance.edit', $maintenance)); ?>" class="btn btn-sm btn-primary">
                <i class="bi bi-pencil"></i> Edit
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><?php echo e($maintenance->title); ?></h5>
                <div>
                    <?php if($maintenance->status === 'RESOLVED'): ?>
                        <span class="badge bg-success">Resolved</span>
                    <?php elseif($maintenance->status === 'IN_PROGRESS'): ?>
                        <span class="badge bg-primary">In Progress</span>
                    <?php elseif($maintenance->status === 'CANCELLED'): ?>
                        <span class="badge bg-secondary">Cancelled</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark">Pending</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6 class="text-muted text-uppercase small">Description</h6>
                    <p class="card-text"><?php echo e($maintenance->description); ?></p>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted text-uppercase small">Issue Type</h6>
                        <p class="fw-bold"><?php echo e($maintenance->type); ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted text-uppercase small">Priority</h6>
                        <p>
                            <?php if($maintenance->priority === 'EMERGENCY'): ?>
                                <span class="text-danger fw-bold"><i class="bi bi-exclamation-triangle-fill"></i> EMERGENCY</span>
                            <?php elseif($maintenance->priority === 'HIGH'): ?>
                                <span class="text-warning fw-bold">High</span>
                            <?php else: ?>
                                <?php echo e(ucfirst(strtolower($maintenance->priority))); ?>

                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <div class="border-top pt-3 mt-2">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase small">Submitted By</h6>
                            <p>
                                <?php echo e($maintenance->tenant->full_name); ?><br>
                                <small class="text-muted"><?php echo e($maintenance->created_at->format('M d, Y h:i A')); ?></small>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase small">Location</h6>
                            <p>
                                <?php echo e($maintenance->unit->building->name); ?><br>
                                Unit <?php echo e($maintenance->unit->unit_number); ?>

                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <?php if($maintenance->resolved_at): ?>
            <div class="card-footer bg-success text-white">
                <i class="bi bi-check-circle-fill me-2"></i> Resolved on <?php echo e($maintenance->resolved_at->format('M d, Y h:i A')); ?>

            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0">Status Actions</h6>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('maintenance.update', $maintenance)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    
                    <input type="hidden" name="title" value="<?php echo e($maintenance->title); ?>">
                    <input type="hidden" name="description" value="<?php echo e($maintenance->description); ?>">
                    <input type="hidden" name="type" value="<?php echo e($maintenance->type); ?>">
                    <input type="hidden" name="priority" value="<?php echo e($maintenance->priority); ?>">
                    
                    <div class="d-grid gap-2">
                        <?php if($maintenance->status !== 'IN_PROGRESS' && $maintenance->status !== 'RESOLVED' && $maintenance->status !== 'CANCELLED'): ?>
                            <button type="submit" name="status" value="IN_PROGRESS" class="btn btn-primary">
                                Mark In Progress
                            </button>
                        <?php endif; ?>

                        <?php if($maintenance->status !== 'RESOLVED' && $maintenance->status !== 'CANCELLED'): ?>
                            <button type="submit" name="status" value="RESOLVED" class="btn btn-success">
                                Mark Resolved
                            </button>
                        <?php endif; ?>

                        <?php if($maintenance->status !== 'CANCELLED'): ?>
                            <button type="submit" name="status" value="CANCELLED" class="btn btn-outline-secondary">
                                Cancel Request
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Program Files\EasyPHP-Devserver-17\eds-www\okaro\resources\views/maintenance/show.blade.php ENDPATH**/ ?>