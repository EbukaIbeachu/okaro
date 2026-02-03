<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Admin Dashboard</h1>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-purple-light mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Total Buildings</h5>
                        <p class="card-text display-4"><?php echo e($stats['total_buildings']); ?></p>
                    </div>
                    <i class="bi bi-building fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-purple-light mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Active Tenants</h5>
                        <p class="card-text display-4"><?php echo e($stats['total_tenants']); ?></p>
                    </div>
                    <i class="bi bi-people-fill fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-purple-light mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">YTD Revenue</h5>
                        <?php
                            $formattedRevenue = number_format($stats['paid_total'], 0);
                            $revLen = strlen($formattedRevenue);
                            // Base size display-5 (smaller than display-4), reduces as length increases
                            $revClass = 'display-5';
                            if ($revLen > 7) $revClass = 'display-6'; // > 999,999 (millions)
                            if ($revLen > 10) $revClass = 'fs-2';     // > 99,999,999 (hundred millions)
                        ?>
                        <p class="card-text <?php echo e($revClass); ?>">₦<?php echo e($formattedRevenue); ?></p>
                    </div>
                    <i class="bi bi-cash-stack fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-purple-light mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Overdue Rents</h5>
                        <p class="card-text display-4"><?php echo e($stats['overdue_count']); ?></p>
                    </div>
                    <i class="bi bi-exclamation-triangle-fill fs-1"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Revenue Chart -->
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Revenue Overview (Last 6 Months)</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Payments -->
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Recent Payments</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Tenant</th>
                                <th>Amount</th>
                                <th>Unit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $recentPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($payment->payment_date->format('M d, Y')); ?></td>
                                <td><?php echo e($payment->rent->tenant->full_name); ?></td>
                                <td class="text-success">₦<?php echo e(number_format($payment->amount, 2)); ?></td>
                                <td><?php echo e($payment->rent->unit->unit_number); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="text-center py-3">No recent payments found.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="<?php echo e(route('payments.index')); ?>" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
        </div>
    </div>

    <!-- Overdue Rents -->
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Overdue Rents</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tenant</th>
                                <th>Unit</th>
                                <th>Due Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $overdueRents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($rent->tenant->full_name); ?></td>
                                <td><?php echo e($rent->unit->building->name); ?> - <?php echo e($rent->unit->unit_number); ?></td>
                                <td class="text-danger fw-bold">₦<?php echo e(number_format($rent->balance, 2)); ?></td>
                                <td>
                                    <a href="<?php echo e(route('payments.create', ['rent_id' => $rent->id])); ?>" class="btn btn-sm btn-primary">Pay</a>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="text-center py-3">No overdue rents.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="<?php echo e(route('rents.index')); ?>" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('revenueChart').getContext('2d');
        var revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chartLabels, 15, 512) ?>,
                datasets: [{
                    label: 'Revenue Collected (₦)',
                    data: <?php echo json_encode($chartData, 15, 512) ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return '₦' + value;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Program Files\EasyPHP-Devserver-17\eds-www\okaro\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>