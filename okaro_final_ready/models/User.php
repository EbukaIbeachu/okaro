


<?php

require_once __DIR__ . '/../core/BaseModel.php';

class User extends BaseModel
{
    public function all(): array
    {
        $sql = 'SELECT u.id, u.name, u.email, r.name AS role_name
                FROM users u
                JOIN roles r ON u.role_id = r.id
                ORDER BY u.name';
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function allWithStatus(): array
    {
        $sql = 'SELECT u.id, u.name, u.email, r.name AS role_name, u.is_active
                FROM users u
                JOIN roles r ON u.role_id = r.id
                ORDER BY u.name';
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $sql = 'SELECT u.id, u.name, u.email, u.role_id, r.name AS role_name
                FROM users u
                JOIN roles r ON u.role_id = r.id
                WHERE u.id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO users
            (name, email, password, role_id)
            VALUES (:name, :email, :password, :role_id)');
        $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
            ':role_id' => $data['role_id'],
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        // If only is_active is being updated (for approval/deactivation)
        if (isset($data['is_active']) && count($data) === 1) {
            $stmt = $this->pdo->prepare('UPDATE users SET is_active = :is_active WHERE id = :id');
            return $stmt->execute([
                ':is_active' => $data['is_active'],
                ':id' => $id,
            ]);
        }

        $params = [
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':role_id' => $data['role_id'],
            ':id' => $id,
        ];

        $sql = 'UPDATE users SET name = :name, email = :email, role_id = :role_id';

        if (!empty($data['password'])) {
            $sql .= ', password = :password';
            $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $sql .= ' WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function roles(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM roles ORDER BY name');
        return $stmt->fetchAll();
    }
}
