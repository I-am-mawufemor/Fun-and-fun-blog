<?php

namespace Mawufemor\Techandfun\Controller;

use Mawufemor\Techandfun\model\Category;

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
        $categories = $this->categoryModel->getAll();
        require ROOT . '/app/view/admin/categories/index.php';
    }

    // Show create form
    public function create(): void
    {
       

        if (!isset($_SESSION['user_id'])) {
            header("Location: ?page=login");
            exit();
        }
        requireAdmin();
        require ROOT . '/app/view/admin/categories/create.php';
    }

    // Handle create form submission
    public function store(): void
    {
   if (!isset($_SESSION['user_id'])) {
            header("Location: ?page=login");
            exit();
        }
        requireAdmin();


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

        $slug = $this->generateSlug($name);

        // Check if category already exists
        if ($this->categoryModel->getByName($name)) {
            echo json_encode(['success' => false, 'message' => 'Category already exist.']);
            exit;
        }


        if ($this->categoryModel->create($name, $slug)) {
            echo json_encode(['success' => true, 'message' => 'Category' . htmlspecialchars($name) . ' created successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create category.']);
        }

        exit;
    }

    // Show edit form
    public function edit(int $id): void
    {
        requireAdmin();

        $category = $this->categoryModel->getById($id);

        if (!$category) {
            $_SESSION['error'] = "Category not found.";
            header("Location: /admin/categories");
            exit;
        }

        require ROOT . '/app/views/admin/categories/edit.php';
    }

    // Handle edit form submission
    public function update(int $id): void
    {
        requireAdmin();

        $name = trim($_POST['name'] ?? '');
        $slug = $this->generateSlug($name);

        if (empty($name)) {
            $_SESSION['error'] = "Category name is required.";
            header("Location: /admin/categories/edit/{$id}");
            exit;
        }

        if ($this->categoryModel->update($id, $name, $slug)) {
            $_SESSION['success'] = "Category updated successfully.";
        } else {
            $_SESSION['error'] = "Failed to update category.";
        }

        header("Location: /admin/categories");
        exit;
    }

    // Handle delete
    public function delete(int $id): void
    {
        requireAdmin();

        if ($this->categoryModel->delete($id)) {
            $_SESSION['success'] = "Category deleted successfully.";
        } else {
            $_SESSION['error'] = "Failed to delete category.";
        }

        header("Location: /admin/categories");
        exit;
    }

    // Auto-generate slug from name
    private function generateSlug(string $name): string
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
    }

    
}
