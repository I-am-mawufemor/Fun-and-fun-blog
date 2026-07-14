<?php

namespace Mawufemor\Techandfun\model;

if (!defined('ROOT')) {
    die("Direct access not allowed");
}

use PDO;
use PDOException;

class Category
{
    public function __construct(private PDO $pdo) {}

    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM categories ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(string $name, string $slug): bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO categories (name, slug) VALUES (?, ?)"
        );
        return $stmt->execute([$name, $slug]);
    }

    public function update(int $id, string $name, string $slug): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE categories SET name = ?, slug = ? WHERE id = ?"
        );
        return $stmt->execute([$name, $slug, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getByName(string $name): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE UPPER(name) = UPPER(?)");
        $stmt->execute([$name]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function exists(int $id): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM categories WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return (bool) $stmt->fetchColumn();
    }
}
