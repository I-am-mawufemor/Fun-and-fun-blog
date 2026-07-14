<?php

namespace Mawufemor\Techandfun\Controller;

use Mawufemor\Techandfun\model\Category;
use Mawufemor\Techandfun\helpers\SlugHelper;

if (!defined('ROOT')) {
    die("Direct access not allowed");
}


use PDO;


class CategoryController
{
    private Category $categoryModel;

    public function __construct(private PDO $pdo)
    {
      
        $this->categoryModel = new Category($this->pdo);
    }

    // List all categories (admin view)
    public function index(): void
    {
        requireLogin();
        requireRole('admin');

        $categories = $this->categoryModel->getAll();
        require ROOT . '/app/view/admin/categories/index.php';
    }

    // Show create form
    public function create(): void
    {


         requireLogin();
        requireRole('admin');
        require ROOT . '/app/view/admin/categories/create.php';
    }

    // Handle create form submission
    public function store(): void
    {
        requireLogin();
        requireRole('admin');


        header('Content-Type: application/json');


        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            exit;
        }
        // validate CSRF token
        validateCSRF($_POST['csrf_token'] ?? '');

        $name = trim($_POST['category_name'] ?? '');


        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit;
        }

        $slug = SlugHelper::generate($name);

        // Check if category already exists
        if ($this->categoryModel->getByName($name)) {
            echo json_encode(['success' => false, 'message' => 'Category already exist.']);
            exit;
        }


        if ($this->categoryModel->create($name, $slug)) {
            echo json_encode(['success' => true, 'message' => 'Category ' . htmlspecialchars($name) . ' created successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create category.']);
        }

        exit;
    }

    // Show edit form
    public function edit(int $id): void
    {
        requireLogin();
        requireRole('admin');

        $category = $this->categoryModel->getById($id);

        if (!$category) {
            echo json_encode(['success' => false, 'message' => 'Category not found.']);
            exit;
        }

        require ROOT . '/app/view/admin/categories/edit.php';
    }

    // Handle edit form submission
    public function update(int $id): void
    {
        requireLogin();
        requireRole('admin');

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            exit;
        }
        // validate CSRF token
        if (!validateCSRF($_POST['csrf_token'] ?? '')) {
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
            exit;
        }

        $name = trim($_POST['category_name'] ?? '');


        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit;
        }

        $slug = SlugHelper::generate($name);

        // Check if category already exists (excluding the current category)
        $existingCategory = $this->categoryModel->getByName($name);
        if ($existingCategory && (int)$existingCategory['id'] !== $id) {
            echo json_encode(['success' => false, 'message' => 'Category already exists.']);
            exit;
        }

        if ($this->categoryModel->update($id, $name, $slug)) {
            echo json_encode(['success' => true, 'message' => 'Category updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update category.']);
        }

        exit;
    }

    // Handle delete
    public function delete(int $id): void
    {
        requireLogin();
        requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Invalid request method.');
        }

        // validate CSRF token
        if (!validateCSRF($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = "Invalid CSRF token.";
            header('Location: ?page=categories');
            exit;
        }

        if ($this->categoryModel->delete($id)) {
            $_SESSION['success'] = "Category deleted successfully.";
        } else {
            $_SESSION['error'] = "Failed to delete category.";
        }

        header('Location: ?page=categories');
        exit;
    }
}
