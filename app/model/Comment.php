<?php

namespace Mawufemor\Techandfun\model;

if (!defined('ROOT')) {
    die('Direct access not allowed');
}

use PDO;

class Comment
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getCommentsByPostId($postId)
    {
        $stmt = $this->pdo->prepare(
            "SELECT 
            c.id,
            c.content,
            c.status,
            c.created_at,
            c.updated_at,
            c.user_id,
            c.post_id,
            u.full_name AS author_name,
            p.title AS post_title
        FROM comments c
        LEFT JOIN users u ON c.user_id = u.id
        INNER JOIN posts p ON c.post_id = p.id
        WHERE c.post_id = :post_id
        AND c.status = 'visible'
        ORDER BY c.created_at DESC"
        );

        $stmt->execute(['post_id' => $postId]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($comments as &$comment) {
            $comment['author_name'] = $comment['user_id']
                ? $comment['author_name']
                : 'Deleted User';
        }

        unset($comment); // break reference after foreach
        return $comments;
    }

    // create a new post
    public function createComment($postId, $userId, $content, $status = 'visible'): int|false
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO comments (post_id, user_id, content, status) 
         VALUES (:post_id, :user_id, :content, :status)"
        );

        $success = $stmt->execute([
            'post_id' => $postId,
            'user_id' => $userId,
            'content' => $content,
            'status' => $status,
        ]);

        return $success ? (int) $this->pdo->lastInsertId() : false;
    }

    // public function getAllComments(): array
    // {
    //    $stmt = $this->pdo->prepare(
    //         "SELECT 
    //         c.id,
    //         c.content,
    //         c.status,
    //         c.created_at,
    //         c.updated_at,
    //         c.user_id,
    //         c.post_id,
    //         u.full_name AS author_name,
    //         p.title AS post_title
    //     FROM comments c
    //     LEFT JOIN users u ON c.user_id = u.id
    //     INNER JOIN posts p ON c.post_id = p.id
    //     ORDER BY c.created_at DESC"
    //     );

    //     $stmt->execute();
    //     $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //     foreach ($comments as &$comment) {
    //         $comment['author_name'] = $comment['user_id']
    //             ? $comment['author_name']
    //             : 'Deleted User';
    //     }
    //     unset($comment); // break reference after foreach

    //     return $comments;
    // }

}
