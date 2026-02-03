<?php
require __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../models/Tenant.php';
require_once __DIR__ . '/../../models/Rent.php';
require_once __DIR__ . '/../../models/Payment.php';
require_once __DIR__ . '/../../models/Request.php';
Auth::startSession();
$userId = $user['id'] ?? null;
$tenantModel = new Tenant();
$rentModel = new Rent();
$paymentModel = new Payment();
$requestModel = new Request();
$tenant = $tenantModel->findByUserId($userId);
$userEmail = $user['email'] ?? '';
if (!$tenant && $userId && $userEmail) {
    $byEmail = $tenantModel->findByEmail($userEmail);
    if ($byEmail) {
        $tenantModel->update((int)$byEmail['id'], ['user_id' => (int)$userId]);
        $tenant = $tenantModel->findByUserId($userId);
    }
}
$unitDetails = $tenant ? $tenant['building_name'] . ' - ' . $tenant['unit_number'] : '';
$rents = $tenant ? $rentModel->forTenant($tenant['id']) : [];
$payments = $tenant ? $paymentModel->forTenant($tenant['id']) : [];
$requests = $tenant ? $requestModel->forTenant($tenant['id']) : [];
$notice = 'No new notices.';
$updateMsg = '';

// Handle contact info update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_contact'])) {
    $newPhone = trim($_POST['phone'] ?? '');
    $newEmail = trim($_POST['email'] ?? '');
    if ($tenant && ($newPhone || $newEmail)) {
        $tenantModel->update($tenant['id'], [
            'phone' => $newPhone,
            'email' => $newEmail,
        ]);
        $updateMsg = 'Contact information updated.';
        // Refresh tenant info
        $tenant = $tenantModel->findByUserId($userId);
    }
}

// Handle maintenance request
$maintenanceMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['maintenance_request'])) {
    $details = trim($_POST['maintenance_details'] ?? '');
    if ($tenant && $details) {
        $requestModel->create([
            'tenant_id' => (int)$tenant['id'],
            'title' => 'Maintenance Request',
            'details' => $details,
            'status' => 'NEW',
        ]);
        $maintenanceMsg = 'Your maintenance request has been submitted.';
        $requests = $requestModel->forTenant($tenant['id']);
    }
}

// Handle payment proof upload
$proofMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_proof'])) {
    $paymentId = (int)($_POST['payment_id'] ?? 0);
    if ($paymentId && isset($_FILES['proof']) && $_FILES['proof']['error'] === UPLOAD_ERR_OK) {
        $payment = $paymentModel->find($paymentId);
        if ($payment) {
            require_once __DIR__ . '/../../models/Rent.php';
            $rentModelCheck = new Rent();
            $rentRow = $rentModelCheck->find((int)$payment['rent_id']);
            if ($rentRow && $tenant && (int)$rentRow['tenant_id'] === (int)$tenant['id']) {
                $uploadDir = __DIR__ . '/../../assets/uploads/payment_proofs/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $ext = pathinfo($_FILES['proof']['name'], PATHINFO_EXTENSION);
                $safeExt = preg_replace('/[^a-zA-Z0-9]/', '', $ext);
                $fileName = 'tenant_' . (int)$tenant['id'] . '_pay_' . (int)$paymentId . '_' . time() . ($safeExt ? ('.' . $safeExt) : '');
                $targetPath = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES['proof']['tmp_name'], $targetPath)) {
                    $newNotes = trim(($payment['notes'] ?? '') . "\nProof: assets/uploads/payment_proofs/" . $fileName);
                    $paymentModel->update($paymentId, [
                        'rent_id' => (int)$payment['rent_id'],
                        'payment_date' => $payment['payment_date'],
                        'amount' => (float)$payment['amount'],
                        'method' => $payment['method'],
                        'reference' => $payment['reference'],
                        'notes' => $newNotes,
                    ]);
                    $proofMsg = 'Proof uploaded successfully.';
                    $payments = $paymentModel->forTenant($tenant['id']);
                } else {
                    $proofMsg = 'Failed to save file.';
                }
            } else {
                $proofMsg = 'Invalid payment selection.';
            }
        }
    }
}
?>

<h1>Tenant Dashboard</h1>

<section class="dashboard-section">
    <h2>My Details</h2>
    <?php if ($tenant): ?>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($tenant['full_name']); ?></p>
        <p><strong>Unit:</strong> <?php echo htmlspecialchars($unitDetails); ?></p>
        <p><strong>Room:</strong> <?php echo htmlspecialchars($tenant['room_number'] ?? ''); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($tenant['phone']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($tenant['email']); ?></p>
        <p><strong>Move In:</strong> <?php echo htmlspecialchars($tenant['move_in_date']); ?></p>
        <p><strong>Move Out:</strong> <?php echo htmlspecialchars($tenant['move_out_date'] ?? ''); ?></p>
        <p><strong>Status:</strong> <?php echo (int)$tenant['active'] ? 'Active' : 'Inactive'; ?></p>
        <form method="post" class="form">
            <h3>Update Contact Info</h3>
            <?php if ($updateMsg): ?><div class="alert alert-success"><?php echo $updateMsg; ?></div><?php endif; ?>
            <label>Phone<br><input type="tel" name="phone" autocomplete="tel" value="<?php echo htmlspecialchars($tenant['phone']); ?>"></label><br>
            <label>Email<br><input type="email" name="email" autocomplete="email" value="<?php echo htmlspecialchars($tenant['email']); ?>"></label><br>
            <button type="submit" name="update_contact" class="btn-primary">Update</button>
        </form>
    <?php else: ?>
        <p>No tenant record found.</p>
    <?php endif; ?>
</section>

<section class="dashboard-section">
    <h2>My Rent History</h2>
    <?php if ($rents && count($rents)): ?>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Annual Amount</th>
                        <th>Due Day</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rents as $r): ?>
                    <tr>
                        <td><?php echo number_format((float)($r['annual_amount'] ?? 0), 2); ?></td>
                        <td><?php echo htmlspecialchars($r['due_day']); ?></td>
                        <td><?php echo htmlspecialchars($r['start_date']); ?></td>
                        <td><?php echo htmlspecialchars($r['end_date']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No rent history found.</p>
    <?php endif; ?>
</section>

<section class="dashboard-section">
    <h2>My Payment History</h2>
    <?php if ($proofMsg): ?><div class="alert alert-success"><?php echo htmlspecialchars($proofMsg); ?></div><?php endif; ?>
    <?php if ($payments && count($payments)): ?>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Reference</th>
                        <th>Notes</th>
                        <th>Status</th>
                        <th>Proof</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $p): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($p['payment_date']); ?></td>
                        <td><?php echo htmlspecialchars($p['amount']); ?></td>
                        <td><?php echo htmlspecialchars($p['method']); ?></td>
                        <td><?php echo htmlspecialchars($p['reference']); ?></td>
                        <td><?php echo htmlspecialchars($p['notes']); ?></td>
                        <td>
                            <?php 
                            $status = $p['approval_status'] ?? 'N/A';
                            $color = match($status) {
                                'APPROVED' => 'green',
                                'REJECTED' => 'red',
                                default => 'orange'
                            };
                            echo "<span style='color:$color; font-weight:bold;'>" . htmlspecialchars($status) . "</span>";
                            ?>
                        </td>
                        <td>
                            <?php if (!empty($p['proof_file'])): ?>
                                <a href="<?php echo htmlspecialchars($p['proof_file']); ?>" target="_blank">View</a>
                                <?php if (($p['approval_status'] ?? '') === 'REJECTED'): ?>
                                    <br><small>Re-upload below</small>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php if (empty($p['proof_file']) || ($p['approval_status'] ?? '') === 'REJECTED'): ?>
                            <form method="post" enctype="multipart/form-data" style="display:flex; gap:8px; align-items:center; margin-top:5px;">
                                <input type="hidden" name="payment_id" value="<?php echo (int)$p['id']; ?>">
                                <input type="file" name="proof" accept="image/*,application/pdf" required>
                                <button type="submit" name="upload_proof" class="btn-secondary">Upload</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No payment history found.</p>
    <?php endif; ?>
</section>

<section class="dashboard-section">
    <h2>Submit Maintenance Request</h2>
    <?php if ($maintenanceMsg): ?><div class="alert alert-success"><?php echo $maintenanceMsg; ?></div><?php endif; ?>
    <form method="post" class="form">
        <label>Request Details<br><textarea name="maintenance_details" required></textarea></label><br>
        <button type="submit" name="maintenance_request" class="btn-primary">Submit Request</button>
    </form>
</section>

<section class="dashboard-section">
    <h2>My Requests</h2>
    <?php if ($requests && count($requests)): ?>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Updated</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $rq): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($rq['title']); ?></td>
                        <td><?php echo htmlspecialchars($rq['status']); ?></td>
                        <td><?php echo htmlspecialchars($rq['created_at']); ?></td>
                        <td><?php echo htmlspecialchars($rq['updated_at']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($rq['details'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No requests submitted yet.</p>
    <?php endif; ?>
    </section>

<section class="dashboard-section">
    <h2>Notices</h2>
    <div class="alert alert-info"><?php echo $notice; ?></div>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>
