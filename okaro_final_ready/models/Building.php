<?php

require_once __DIR__ . '/../core/BaseModel.php';

class Building extends BaseModel
{
    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM buildings ORDER BY name');
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM buildings WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO buildings
            (name, address_line1, address_line2, city, state, postal_code, image_path, manager_id)
            VALUES (:name, :address_line1, :address_line2, :city, :state, :postal_code, :image_path, :manager_id)');
        $stmt->execute([
            ':name' => $data['name'],
            ':address_line1' => $data['address_line1'],
            ':address_line2' => $data['address_line2'] ?? null,
            ':city' => $data['city'],
            ':state' => $data['state'],
            ':postal_code' => $data['postal_code'],
            ':image_path' => $data['image_path'] ?? null,
            ':manager_id' => $data['manager_id'] ?? null,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare('UPDATE buildings SET
            name = :name,
            address_line1 = :address_line1,
            address_line2 = :address_line2,
            city = :city,
            state = :state,
            postal_code = :postal_code,
            image_path = :image_path,
            manager_id = :manager_id
            WHERE id = :id');
        return $stmt->execute([
            ':name' => $data['name'],
            ':address_line1' => $data['address_line1'],
            ':address_line2' => $data['address_line2'] ?? null,
            ':city' => $data['city'],
            ':state' => $data['state'],
            ':postal_code' => $data['postal_code'],
            ':image_path' => $data['image_path'] ?? null,
            ':manager_id' => $data['manager_id'] ?? null,
            ':id' => $id,
        ]);
    }

    public function assignManager(int $id, ?int $managerId): bool
    {
        $stmt = $this->pdo->prepare('UPDATE buildings SET manager_id = :manager_id WHERE id = :id');
        return $stmt->execute([
            ':manager_id' => $managerId,
            ':id' => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM buildings WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
