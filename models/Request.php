<?php

require_once __DIR__ . '/../core/BaseModel.php';

class Request extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->ensureTable();
    }

    private function ensureTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS tenant_requests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            details TEXT NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'NEW',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        $this->pdo->exec($sql);
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO tenant_requests
            (tenant_id, title, details, status)
            VALUES (:tenant_id, :title, :details, :status)');
        $stmt->execute([
            ':tenant_id' => $data['tenant_id'],
            ':title' => $data['title'],
            ':details' => $data['details'],
            ':status' => $data['status'] ?? 'NEW',
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function forTenant(int $tenantId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tenant_requests WHERE tenant_id = :tenant_id ORDER BY created_at DESC, id DESC');
        $stmt->execute([':tenant_id' => $tenantId]);
        return $stmt->fetchAll();
    }

    public function recentWithDetails(int $limit = 50): array
    {
        $limit = max(1, min($limit, 200));
        $sql = 'SELECT tr.*, t.full_name, u.unit_number, b.name AS building_name
                FROM tenant_requests tr
                JOIN tenants t ON tr.tenant_id = t.id
                JOIN units u ON t.unit_id = u.id
                JOIN buildings b ON u.building_id = b.id
                ORDER BY tr.created_at DESC, tr.id DESC
                LIMIT ' . (int)$limit;
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function forManager(int $managerUserId, int $limit = 50): array
    {
        $limit = max(1, min($limit, 200));
        $sql = 'SELECT tr.*, t.full_name, u.unit_number, b.name AS building_name
                FROM tenant_requests tr
                JOIN tenants t ON tr.tenant_id = t.id
                JOIN units u ON t.unit_id = u.id
                JOIN buildings b ON u.building_id = b.id
                WHERE b.manager_id = :manager_id
                ORDER BY tr.created_at DESC, tr.id DESC
                LIMIT ' . (int)$limit;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':manager_id' => $managerUserId]);
        return $stmt->fetchAll();
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->pdo->prepare('UPDATE tenant_requests SET status = :status WHERE id = :id');
        return $stmt->execute([
            ':status' => $status,
            ':id' => $id,
        ]);
    }
}

