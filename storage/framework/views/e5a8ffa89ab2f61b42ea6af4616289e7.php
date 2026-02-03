<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Tenant Profile</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?php echo e(route('tenants.index')); ?>" class="btn btn-sm btn-secondary me-2">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
        <a href="<?php echo e(route('tenants.edit', $tenant)); ?>" class="btn btn-sm btn-primary">
            <i class="bi bi-pencil"></i> Edit Tenant
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-body text-center">
                <?php if($tenant->profile_image): ?>
                    <img src="<?php echo e(asset('storage/' . $tenant->profile_image)); ?>" alt="<?php echo e($tenant->full_name); ?>" class="rounded-circle object-fit-cover mx-auto mb-3" style="width: 120px; height: 120px;">
                <?php else: ?>
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-person fs-1"></i>
                    </div>
                <?php endif; ?>
                <h4 class="card-title"><?php echo e($tenant->full_name); ?></h4>
                <?php if(auth()->user()->isAdmin() && $tenant->creator): ?>
                    <div class="mb-2 text-muted small">
                        <i class="bi bi-person-check me-1"></i> Registered by: <?php echo e($tenant->creator->name); ?>

                    </div>
                <?php endif; ?>
                <p class="text-muted mb-2">
                    <?php if($tenant->active): ?>
                        <span class="badge bg-success">Active Tenant</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Former Tenant</span>
                    <?php endif; ?>
                </p>
                
                <hr>
                
                <div class="text-start">
                    <p class="mb-1"><i class="bi bi-envelope me-2 text-muted"></i> <?php echo e($tenant->email ?? 'N/A'); ?></p>
                    <p class="mb-1"><i class="bi bi-telephone me-2 text-muted"></i> <?php echo e($tenant->phone ?? 'N/A'); ?></p>
                    <p class="mb-1"><i class="bi bi-calendar-event me-2 text-muted"></i> Since <?php echo e($tenant->move_in_date); ?></p>
                    <?php if($tenant->move_out_date): ?>
                        <p class="mb-1"><i class="bi bi-calendar-x me-2 text-muted"></i> Left <?php echo e($tenant->move_out_date); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8 mb-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">
                Current Residence
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="mb-1"><?php echo e($tenant->unit->building->name); ?></h5>
                        <p class="mb-0 text-muted">
                            Unit <?php echo e($tenant->unit->unit_number); ?><br>
                            <?php echo e($tenant->unit->building->address_line1); ?>, <?php echo e($tenant->unit->building->city); ?>

                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <a href="<?php echo e(route('units.show', $tenant->unit)); ?>" class="btn btn-outline-primary btn-sm">View Unit</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold">Lease Agreements</span>
                <a href="<?php echo e(route('rents.create', ['tenant_id' => $tenant->id])); ?>" class="btn btn-sm btn-success">
                    <i class="bi bi-plus-lg"></i> New Lease
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Period</th>
                                <th>Amount</th>
                                <th>Due Day</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $tenant->rents->sortByDesc('start_date'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <?php echo e($rent->start_date); ?> <span class="text-muted mx-1">to</span> <?php echo e($rent->end_date); ?>

                                </td>
                                <td>₦<?php echo e(number_format($rent->annual_amount, 2)); ?></td>
                                <td><?php echo e($rent->due_day); ?>th</td>
                                <td>
                                    <?php if($rent->status === 'ACTIVE'): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?php echo e(ucfirst(strtolower($rent->status))); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="<?php echo e(route('rents.show', $rent)); ?>" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">
                                    No lease agreements found.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold">Payment History</span>
                <a href="<?php echo e(route('payments.create', ['tenant_id' => $tenant->id])); ?>" class="btn btn-sm btn-success">
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
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $tenant->payments->sortByDesc('payment_date')->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
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
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">
                                    No payments recorded.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if($tenant->payments->count() > 5): ?>
                <div class="card-footer bg-white text-center">
                    <a href="<?php echo e(route('payments.index', ['tenant_id' => $tenant->id])); ?>" class="text-decoration-none">View All Payments</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Program Files\EasyPHP-Devserver-17\eds-www\okaro\resources\views/tenants/show.blade.php ENDPATH**/ ?>