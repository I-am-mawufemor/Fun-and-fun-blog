<?php

namespace Mawufemor\Techandfun\controller;

use Mawufemor\Techandfun\model\Comment;
use Mawufemor\Techandfun\model\Post;

use PDO;

if (!defined('ROOT')) {
    die('Direct access not allowed');
}

class CommentController
{
    private PDO $pdo;
    private Comment $commentModel;
    private Post $postModel;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->commentModel = new Comment($this->pdo);
        $this->postModel = new Post($this->pdo);
    }

    public function getCommentsByPostId(int $postId): array
    {
        return $this->commentModel->getCommentsByPostId($postId);
    }


    public function createComment(int $postId, string $content)
    {
        requireLogin();

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
            exit;
        }

        if (!validateCSRF($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
            exit;
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
            exit;
        }

        $content = trim($content);
        if ($content === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Content cannot be empty.']);
            exit;
        }

        if (mb_strlen($content) > 2000) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Comment is too long.']);
            exit;
        }

        // Confirm the post actually exists before attempting to comment on it
        if (!$this->postModel->getPostById($postId)) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Post not found.']);
            exit;
        }

        try {
            $commentId = $this->commentModel->createComment($postId, $userId, $content);

            if ($commentId === false) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to create comment.']);
                exit;
            }

            http_response_code(201);
            echo json_encode(['success' => true, 'id' => $commentId]);
        } catch (\PDOException $e) {
            error_log('Comment creation failed: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'A database error occurred.']);
        }

        exit;
    }
}
