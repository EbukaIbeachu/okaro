<?php require __DIR__ . '/../partials/header.php'; ?>

<h1><?php echo Auth::hasRole(['admin']) ? 'Admin Dashboard' : 'Manager Dashboard'; ?></h1>

<section class="dashboard-grid">
    <div class="card stat-card">
        <div class="stat-label">Buildings</div>
        <div class="stat-value"><?php echo (int)($stats['total_buildings'] ?? 0); ?></div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">Tenants</div>
        <div class="stat-value"><?php echo (int)($stats['total_tenants'] ?? 0); ?></div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">Rent Collected</div>
        <div class="stat-value">₦<?php echo number_format($stats['paid_total'] ?? 0, 2); ?></div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">Overdue Rents</div>
        <div class="stat-value"><?php echo (int)($stats['overdue_count'] ?? 0); ?></div>
    </div>
</section>

<section class="dashboard-section">
    <div class="section-header">
        <h2>Tenant Requests</h2>
    </div>
    <?php if (empty($requests)): ?>
        <p>No tenant requests.</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Building</th>
                        <th>Unit</th>
                        <th>Tenant</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Details</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $rq): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($rq['building_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($rq['unit_number'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($rq['full_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($rq['title']); ?></td>
                            <td><?php echo htmlspecialchars($rq['status']); ?></td>
                            <td><?php echo htmlspecialchars($rq['created_at']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($rq['details'])); ?></td>
                            <td>
                                <form method="post" action="/okaro/index.php?route=admin/dashboard" class="form" style="display:flex; gap:8px; align-items:center;">
                                    <input type="hidden" name="request_id" value="<?php echo (int)$rq['id']; ?>">
                                    <select name="status">
                                        <option value="NEW" <?php echo ($rq['status']==='NEW')?'selected':''; ?>>NEW</option>
                                        <option value="IN_PROGRESS" <?php echo ($rq['status']==='IN_PROGRESS')?'selected':''; ?>>IN_PROGRESS</option>
                                        <option value="RESOLVED" <?php echo ($rq['status']==='RESOLVED')?'selected':''; ?>>RESOLVED</option>
                                    </select>
                                    <button type="submit" name="update_request_status" class="btn-primary">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<section class="dashboard-section">
    <div class="section-header">
        <h2>Pending Payment Proofs</h2>
    </div>
    <?php if (empty($pendingProofs)): ?>
        <p>No pending proofs.</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Building</th>
                        <th>Unit</th>
                        <th>Tenant</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Proof</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingProofs as $pp): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($pp['building_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($pp['unit_number'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($pp['full_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($pp['amount']); ?></td>
                            <td><?php echo htmlspecialchars($pp['payment_date']); ?></td>
                            <td>
                                <a href="<?php echo htmlspecialchars($pp['proof_file']); ?>" target="_blank" class="btn-secondary">View Proof</a>
                            </td>
                            <td>
                                <form method="post" action="/okaro/index.php?route=admin/dashboard" style="display:flex; gap:8px;">
                                    <input type="hidden" name="payment_id" value="<?php echo (int)$pp['id']; ?>">
                                    <input type="hidden" name="update_payment_status" value="1">
                                    <button type="submit" name="status" value="APPROVED" class="btn-primary" style="background-color:green;border:none;">Approve</button>
                                    <button type="submit" name="status" value="REJECTED" class="btn-primary" style="background-color:red;border:none;">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<div class="dashboard-grid" style="grid-template-columns: 1fr;">
    <div class="card">
        <h3>Revenue Overview</h3>
        <canvas id="revenueChart" height="100"></canvas>
    </div>
</div>

<section class="dashboard-section">
    <div class="section-header">
        <h2>Overdue Rents (Annual)</h2>
        <input type="text" id="overdueSearch" placeholder="Search by tenant, building, unit..." autocomplete="off" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
        <div id="overdueAutocomplete" class="autocomplete-list"></div>
    </div>
    <?php if (empty($overdueRents)): ?>
        <p>No overdue rents for the current month.</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table class="table" id="overdueTable">
                <thead>
                    <tr>
                        <th>Building</th>
                        <th>Unit</th>
                        <th>Tenant</th>
                        <th>Annual Rent</th>
                        <th>Due Day</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($overdueRents as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['building_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['unit_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                            <td>₦<?php echo number_format((float)($row['annual_amount'] ?? 0), 2); ?></td>
                            <td><?php echo (int)$row['due_day']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<section class="dashboard-section">
    <div class="section-header">
        <h2>Recent Payments</h2>
        <input type="text" id="paymentSearch" placeholder="Search by tenant, building, unit..." autocomplete="off" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
        <div id="paymentAutocomplete" class="autocomplete-list"></div>
    </div>
    <?php if (empty($recentPayments)): ?>
        <p>No payments recorded yet.</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table class="table" id="paymentTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Building</th>
                        <th>Unit</th>
                        <th>Tenant</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Reference</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentPayments as $p): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($p['payment_date']); ?></td>
                            <td><?php echo htmlspecialchars($p['building_name']); ?></td>
                            <td><?php echo htmlspecialchars($p['unit_number']); ?></td>
                            <td><?php echo htmlspecialchars($p['full_name']); ?></td>
                            <td>₦<?php echo number_format($p['amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($p['method'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($p['reference'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<script>
    // Chart.js Initialization
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChart');
        if (ctx) {
            const revenueChart = new Chart(ctx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['Rent Collected'],
                    datasets: [{
                        label: 'Amount (₦)',
                        data: [<?php echo $stats['paid_total'] ?? 0; ?>],
                        backgroundColor: ['rgba(16, 185, 129, 0.5)'],
                        borderColor: ['rgba(16, 185, 129, 1)'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Table Search Functionality with Autocomplete
        function setupTableSearch(inputId, tableId, autocompleteId) {
            const input = document.getElementById(inputId);
            const table = document.getElementById(tableId);
            const autocomplete = document.getElementById(autocompleteId);
            if (!input || !table || !autocomplete) return;

            input.addEventListener('keyup', function() {
                const filter = input.value.toLowerCase();
                const rows = table.getElementsByTagName('tr');
                let suggestions = [];
                for (let i = 1; i < rows.length; i++) { // Start from 1 to skip header
                    const cells = rows[i].getElementsByTagName('td');
                    let found = false;
                    for (let j = 0; j < cells.length; j++) {
                        const txtValue = cells[j].textContent || cells[j].innerText;
                        if (txtValue.toLowerCase().indexOf(filter) > -1) {
                            found = true;
                            if (filter && txtValue.toLowerCase().startsWith(filter)) {
                                suggestions.push(txtValue);
                            }
                            break;
                        }
                    }
                    rows[i].style.display = found ? '' : 'none';
                }
                // Autocomplete
                autocomplete.innerHTML = '';
                if (filter && suggestions.length) {
                    suggestions = [...new Set(suggestions)].slice(0, 8);
                    suggestions.forEach(function(s) {
                        const div = document.createElement('div');
                        div.className = 'autocomplete-item';
                        div.textContent = s;
                        div.onclick = function() {
                            input.value = s;
                            autocomplete.innerHTML = '';
                            input.dispatchEvent(new Event('keyup'));
                        };
                        autocomplete.appendChild(div);
                    });
                }
            });
            document.addEventListener('click', function(e) {
                if (e.target !== input) autocomplete.innerHTML = '';
            });
        }

        // Initialize search functionality
        setupTableSearch('overdueSearch', 'overdueTable', 'overdueAutocomplete');
        setupTableSearch('paymentSearch', 'paymentTable', 'paymentAutocomplete');
    });
</script>

                    // Initialize table searches with autocomplete
                    setupTableSearch('overdueSearch', 'overdueTable', 'overdueAutocomplete');
                    setupTableSearch('paymentSearch', 'paymentTable', 'paymentAutocomplete');
                </script>
                <style>
                .autocomplete-list { position: absolute; background: #fff; border: 1px solid #ccc; z-index: 10; max-height: 180px; overflow-y: auto; width: 250px; }
                .autocomplete-item { padding: 6px 12px; cursor: pointer; }
                .autocomplete-item:hover { background: #f0f0f0; }
                </style>
                <tr>
                    <td><?php echo htmlspecialchars($b['name']); ?></td>
                    <td>
                        <form method="post" action="/okaro/index.php?route=admin/dashboard" class="form" style="display:flex; gap:8px; align-items:center;">
                            <input type="hidden" name="building_id" value="<?php echo (int)$b['id']; ?>">
                            <select name="manager_id" <?php echo Auth::hasRole(['admin']) ? '' : 'disabled'; ?>>
                                <option value="" <?php echo $currentManagerId ? '' : 'selected'; ?>>Unassigned</option>
                                <?php foreach ($managerOptions as $m): ?>
                                    <option value="<?php echo (int)$m['id']; ?>" <?php echo ($currentManagerId == $m['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($m['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="assign_building_manager" class="btn-primary" <?php echo Auth::hasRole(['admin']) ? '' : 'disabled title="Admin only"'; ?>>Save</button>
                        </form>
                    </td>
                    <td><?php echo $currentManagerName ? htmlspecialchars($currentManagerName) : 'Unassigned'; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<section class="dashboard-section">
    <div class="section-header">
        <h2>Manage Units</h2>
        <a href="/okaro/units.php" class="btn-primary">View All Units</a>
        <a href="/okaro/units.php?action=add" class="btn-secondary">Add Unit</a>
    </div>
    <!-- Table of units would go here -->
</section>

<section class="dashboard-section">
    <div class="section-header">
        <h2>Manage Tenants</h2>
        <a href="/okaro/tenants.php" class="btn-primary">View All Tenants</a>
        <a href="/okaro/tenants.php?action=add" class="btn-secondary">Add Tenant</a>
    </div>
    <!-- Table of tenants would go here -->
</section>

<section class="dashboard-section">
    <div class="section-header">
        <h2>Manage Rents</h2>
        <a href="/okaro/rents.php" class="btn-primary">View All Rents</a>
        <a href="/okaro/rents.php?action=add" class="btn-secondary">Add Rent</a>
    </div>
    <!-- Table of rents would go here -->
</section>

<section class="dashboard-section">
    <div class="section-header">
        <h2>Manage Payments</h2>
        <a href="/okaro/payments.php" class="btn-primary">View All Payments</a>
        <a href="/okaro/payments.php?action=add" class="btn-secondary">Add Payment</a>
    </div>
    <!-- Table of payments would go here -->
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>
