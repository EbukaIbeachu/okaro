<?php

require_once __DIR__ . '/../core/BaseModel.php';

class Unit extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->ensureCompositeUniqueIndex();
    }
    public function existsInBuilding(int $buildingId, string $unitNumber): bool
    {
        $stmt = $this->pdo->prepare('SELECT id FROM units WHERE building_id = :building_id AND unit_number = :unit_number LIMIT 1');
        $stmt->execute([
            ':building_id' => $buildingId,
            ':unit_number' => $unitNumber,
        ]);
        return (bool)$stmt->fetchColumn();
    }

    public function all(): array
    {
        $sql = 'SELECT * FROM units ORDER BY id DESC';
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
    public function allWithBuildings(): array
    {
        $sql = 'SELECT u.*, b.name AS building_name
                FROM units u
                JOIN buildings b ON u.building_id = b.id
                ORDER BY b.name, u.unit_number';
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function byBuilding(int $buildingId): array
    {
        $sql = 'SELECT * FROM units WHERE building_id = :building_id ORDER BY unit_number';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':building_id' => $buildingId]);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM units WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByBuildingAndNumber(int $buildingId, string $unitNumber): ?array
    {
        $sql = 'SELECT * FROM units WHERE building_id = :building_id AND unit_number = :unit_number LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':building_id' => $buildingId, ':unit_number' => $unitNumber]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByBuildingNumberFloor(int $buildingId, string $unitNumber, ?string $floor): ?array
    {
        $sql = 'SELECT * FROM units WHERE building_id = :building_id AND unit_number = :unit_number AND (floor <=> :floor) LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':building_id' => $buildingId,
            ':unit_number' => $unitNumber,
            ':floor' => ($floor === '' ? null : $floor),
        ]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO units
            (building_id, unit_number, floor, bedrooms, bathrooms, status)
            VALUES (:building_id, :unit_number, :floor, :bedrooms, :bathrooms, :status)');
        $stmt->execute([
            ':building_id' => $data['building_id'],
            ':unit_number' => $data['unit_number'],
            ':floor' => $data['floor'] ?? null,
            ':bedrooms' => $data['bedrooms'] ?? 0,
            ':bathrooms' => $data['bathrooms'] ?? 0,
            ':status' => $data['status'] ?? 'AVAILABLE',
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare('UPDATE units SET
            building_id = :building_id,
            unit_number = :unit_number,
            floor = :floor,
            bedrooms = :bedrooms,
            bathrooms = :bathrooms,
            status = :status
            WHERE id = :id');
        return $stmt->execute([
            ':building_id' => $data['building_id'],
            ':unit_number' => $data['unit_number'],
            ':floor' => $data['floor'] ?? null,
            ':bedrooms' => $data['bedrooms'] ?? 0,
            ':bathrooms' => $data['bathrooms'] ?? 0,
            ':status' => $data['status'] ?? 'AVAILABLE',
            ':id' => $id,
        ]);
    }

    public function existsByComposite(int $buildingId, string $unitNumber, ?string $floor, ?int $excludeId = null): bool
    {
        $sql = 'SELECT id FROM units WHERE building_id = :building_id AND unit_number = :unit_number AND (floor <=> :floor)';
        if ($excludeId) {
            $sql .= ' AND id <> :exclude_id';
        }
        $stmt = $this->pdo->prepare($sql);
        $params = [
            ':building_id' => $buildingId,
            ':unit_number' => $unitNumber,
            ':floor' => ($floor === '' ? null : $floor),
        ];
        if ($excludeId) {
            $params[':exclude_id'] = $excludeId;
        }
        $stmt->execute($params);
        return (bool)$stmt->fetchColumn();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM units WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    private function ensureCompositeUniqueIndex(): void
    {
        try {
            $this->pdo->exec('ALTER TABLE units DROP INDEX uq_units_building_unit');
        } catch (PDOException $e) {
        }
        try {
            $this->pdo->exec('CREATE UNIQUE INDEX uq_units_building_unit_floor ON units(building_id, unit_number, floor)');
        } catch (PDOException $e) {
        }
    }
}
