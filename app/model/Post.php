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

    public function getPosts()
    {
        $stmt = $this->pdo->query("SELECT * FROM posts");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getPostById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM posts WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createPost($title, $content, $author_id)
    {
        $stmt = $this->pdo->prepare("INSERT INTO posts (title, content, author_id) VALUES (:title, :content, :author_id)");
        return $stmt->execute(['title' => $title, 'content' => $content, 'author_id' => $author_id]);
    }
}
?>