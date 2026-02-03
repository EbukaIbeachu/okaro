<?php

require_once __DIR__ . '/../core/BaseModel.php';

class Payment extends BaseModel
{
    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare('UPDATE payments SET
            rent_id = :rent_id,
            payment_date = :payment_date,
            amount = :amount,
            method = :method,
            reference = :reference,
            notes = :notes
            WHERE id = :id');
        return $stmt->execute([
            ':rent_id' => $data['rent_id'],
            ':payment_date' => $data['payment_date'],
            ':amount' => $data['amount'],
            ':method' => $data['method'] ?? null,
            ':reference' => $data['reference'] ?? null,
            ':notes' => $data['notes'] ?? null,
            ':id' => $id,
        ]);
    }
    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM payments WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
    public function forRent(int $rentId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM payments WHERE rent_id = :rent_id ORDER BY payment_date DESC');
        $stmt->execute([':rent_id' => $rentId]);
        return $stmt->fetchAll();
    }

    public function forTenant(int $tenantId): array
    {
        $sql = 'SELECT p.*, r.annual_amount, t.full_name, u.unit_number, b.name AS building_name
                FROM payments p
                JOIN rents r ON p.rent_id = r.id
                JOIN tenants t ON r.tenant_id = t.id
                JOIN units u ON r.unit_id = u.id
                JOIN buildings b ON u.building_id = b.id
                WHERE t.id = :tenant_id
                ORDER BY p.payment_date DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':tenant_id' => $tenantId]);
        return $stmt->fetchAll();
    }

    public function updateApprovalStatus(int $paymentId, string $status): bool
    {
        $stmt = $this->pdo->prepare('UPDATE payments SET approval_status = :status WHERE id = :id');
        return $stmt->execute([':status' => $status, ':id' => $paymentId]);
    }

    public function getPendingProofs(): array
    {
        $sql = "SELECT p.*, r.annual_amount, t.full_name, u.unit_number, b.name AS building_name
                FROM payments p
                JOIN rents r ON p.rent_id = r.id
                JOIN tenants t ON r.tenant_id = t.id
                JOIN units u ON r.unit_id = u.id
                JOIN buildings b ON u.building_id = b.id
                WHERE p.proof_file IS NOT NULL AND p.approval_status = 'PENDING'
                ORDER BY p.payment_date ASC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO payments
            (rent_id, payment_date, amount, method, reference, notes, proof_file, approval_status)
            VALUES (:rent_id, :payment_date, :amount, :method, :reference, :notes, :proof_file, :approval_status)');
        $stmt->execute([
            ':rent_id' => $data['rent_id'],
            ':payment_date' => $data['payment_date'],
            ':amount' => $data['amount'],
            ':method' => $data['method'] ?? null,
            ':reference' => $data['reference'] ?? null,
            ':notes' => $data['notes'] ?? null,
            ':proof_file' => $data['proof_file'] ?? null,
            ':approval_status' => $data['approval_status'] ?? 'PENDING',
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function monthlySummaryForCurrentMonth(): array
    {
        $sql = 'SELECT 
                    SUM(r.annual_amount / 12) AS expected_total,
                    (SELECT SUM(p.amount) FROM payments p
                     WHERE YEAR(p.payment_date) = YEAR(CURDATE())
                       AND MONTH(p.payment_date) = MONTH(CURDATE())) AS paid_total
                FROM rents r
                WHERE (r.start_date <= LAST_DAY(CURDATE()))
                  AND (r.end_date IS NULL OR r.end_date >= DATE_SUB(CURDATE(), INTERVAL DAY(CURDATE())-1 DAY))';
        $stmt = $this->pdo->query($sql);
        $row = $stmt->fetch();
        return $row ?: ['expected_total' => 0, 'paid_total' => 0];
    }

    public function overdueRents(): array
    {
        $sql = "SELECT r.*, t.full_name, u.unit_number, b.name AS building_name
                FROM rents r
                JOIN tenants t ON r.tenant_id = t.id
                JOIN units u ON r.unit_id = u.id
                JOIN buildings b ON u.building_id = b.id
                WHERE t.active = 1
                  AND (r.end_date IS NULL OR r.end_date >= CURDATE())
                  AND r.start_date <= CURDATE()
                  AND NOT EXISTS (
                    SELECT 1 FROM payments p
                    WHERE p.rent_id = r.id
                      AND YEAR(p.payment_date) = YEAR(CURDATE())
                      AND MONTH(p.payment_date) = MONTH(CURDATE())
                )";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function recent(int $limit = 10): array
    {
        $limit = max(1, min($limit, 50));
        $sql = 'SELECT p.*, t.full_name, u.unit_number, b.name AS building_name
                FROM payments p
                LEFT JOIN rents r ON p.rent_id = r.id
                LEFT JOIN tenants t ON r.tenant_id = t.id
                LEFT JOIN units u ON r.unit_id = u.id
                LEFT JOIN buildings b ON u.building_id = b.id
                ORDER BY p.payment_date DESC, p.id DESC
                LIMIT ' . (int)$limit;
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
    public function totalCollected(): float
    {
        $stmt = $this->pdo->query('SELECT COALESCE(SUM(amount), 0) AS total FROM payments');
        $row = $stmt->fetch();
        return (float)($row['total'] ?? 0.0);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM payments WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
