<?php

require_once __DIR__ . '/../core/BaseModel.php';

class Rent extends BaseModel
{
    public function all(): array
    {
        $sql = 'SELECT * FROM rents ORDER BY id DESC';
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM rents WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
    public function forTenant(int $tenantId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM rents WHERE tenant_id = :tenant_id');
        $stmt->execute([':tenant_id' => $tenantId]);
        return $stmt->fetchAll();
    }

    public function allWithTenant(): array
    {
        $sql = 'SELECT r.*, r.annual_amount, t.full_name, t.room_number, u.unit_number, b.name AS building_name
                FROM rents r
                JOIN tenants t ON r.tenant_id = t.id
                JOIN units u ON r.unit_id = u.id
                JOIN buildings b ON u.building_id = b.id
                ORDER BY annual_amount DESC, b.name, u.unit_number, t.full_name';
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO rents
            (tenant_id, unit_id, annual_amount, due_day, start_date, end_date)
            VALUES (:tenant_id, :unit_id, :annual_amount, :due_day, :start_date, :end_date)');
        $stmt->execute([
            ':tenant_id' => $data['tenant_id'],
            ':unit_id' => $data['unit_id'],
            ':annual_amount' => $data['annual_amount'],
            ':due_day' => $data['due_day'],
            ':start_date' => $data['start_date'],
            ':end_date' => $data['end_date'] ?? null,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare('UPDATE rents SET
            tenant_id = :tenant_id,
            unit_id = :unit_id,
            annual_amount = :annual_amount,
            due_day = :due_day,
            start_date = :start_date,
            end_date = :end_date
            WHERE id = :id');
        return $stmt->execute([
            ':tenant_id' => $data['tenant_id'],
            ':unit_id' => $data['unit_id'],
            ':annual_amount' => $data['annual_amount'],
            ':due_day' => $data['due_day'],
            ':start_date' => $data['start_date'],
            ':end_date' => $data['end_date'] ?? null,
            ':id' => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM rents WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
