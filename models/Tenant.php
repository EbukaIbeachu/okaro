<?php

require_once __DIR__ . '/../core/BaseModel.php';

class Tenant extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->ensureRoomNumberColumn();
    }

    public function allWithUnits(): array
    {
        $sql = 'SELECT t.*, u.unit_number, b.name AS building_name
                FROM tenants t
                JOIN units u ON t.unit_id = u.id
                JOIN buildings b ON u.building_id = b.id
                ORDER BY b.name, u.unit_number, t.full_name';
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $sql = 'SELECT t.*, u.unit_number, b.name AS building_name
                FROM tenants t
                JOIN units u ON t.unit_id = u.id
                JOIN buildings b ON u.building_id = b.id
                WHERE t.id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByUserId(int $userId): ?array
    {
        $sql = 'SELECT t.*, u.unit_number, b.name AS building_name
                FROM tenants t
                JOIN units u ON t.unit_id = u.id
                JOIN buildings b ON u.building_id = b.id
                WHERE t.user_id = :user_id AND t.active = 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
    public function findByEmail(string $email): ?array
    {
        $sql = 'SELECT t.*, u.unit_number, b.name AS building_name
                FROM tenants t
                JOIN units u ON t.unit_id = u.id
                JOIN buildings b ON u.building_id = b.id
                WHERE t.email = :email AND t.active = 1
                ORDER BY t.id DESC LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO tenants
            (user_id, unit_id, full_name, phone, email, room_number, move_in_date, move_out_date, active)
            VALUES (:user_id, :unit_id, :full_name, :phone, :email, :room_number, :move_in_date, :move_out_date, :active)');
        $stmt->execute([
            ':user_id' => $data['user_id'] ?? null,
            ':unit_id' => $data['unit_id'],
            ':full_name' => $data['full_name'],
            ':phone' => $data['phone'] ?? null,
            ':email' => $data['email'] ?? null,
            ':room_number' => $data['room_number'] ?? null,
            ':move_in_date' => $data['move_in_date'],
            ':move_out_date' => $data['move_out_date'] ?? null,
            ':active' => $data['active'] ?? 1,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $allowed = ['user_id','unit_id','full_name','phone','email','room_number','move_in_date','move_out_date','active'];
        $sets = [];
        $params = [':id' => $id];
        foreach ($allowed as $key) {
            if (array_key_exists($key, $data)) {
                $sets[] = "$key = :$key";
                $params[":$key"] = $data[$key];
            }
        }
        if (empty($sets)) {
            return false;
        }
        $sql = 'UPDATE tenants SET ' . implode(', ', $sets) . ' WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM tenants WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    private function ensureRoomNumberColumn(): void
    {
        try {
            $this->pdo->exec('ALTER TABLE tenants ADD COLUMN room_number VARCHAR(50) NULL AFTER email');
        } catch (PDOException $e) {
        }
    }
}
