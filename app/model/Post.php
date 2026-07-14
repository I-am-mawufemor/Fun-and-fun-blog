<?php

namespace Mawufemor\Techandfun\Model;

if (!defined('ROOT')) {
    die("Direct access not allowed");
}

use PDO;
use PDOException;

class Post
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

public function getAllPosts(): array
{
    $stmt = $this->pdo->prepare(
        "SELECT 
            p.id,
            p.title,
            p.slug,
            p.content,
            p.status,
            p.created_at,
            p.updated_at,
            p.user_id,
            p.category_id,
            u.full_name AS author_name,
            c.name AS category_name
        FROM posts p
        LEFT JOIN users u ON p.user_id = u.id
        INNER JOIN categories c ON p.category_id = c.id
        ORDER BY p.created_at DESC"
    );

    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($posts as &$post) {
        $post['author_name'] = $post['user_id']
            ? $post['author_name']
            : 'Deleted User';
    }
    unset($post); // break reference after foreach

    return $posts;
}    
    public function getPostById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM posts WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

   public function createPost($title, $slug, $content, $user_id, $category_id)
{
    $stmt = $this->pdo->prepare(
        "INSERT INTO posts (title, slug, content, user_id, category_id) 
         VALUES (:title, :slug, :content, :user_id, :category_id)"
    );

    return $stmt->execute([
        'title'       => $title,
        'slug'        => $slug,
        'content'     => $content,
        'user_id'     => $user_id,
        'category_id' => $category_id,
    ]);
}

    public function slugExists(string $slug): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM posts WHERE slug = ? LIMIT 1');
        $stmt->execute([$slug]);
        return (bool) $stmt->fetchColumn();
    }
}
