<?php
// Ensure the user is authenticated and is an admin!
require_once '../users/auth.php';
requireAdmin();

// Include database configuration and page header
require_once '../db/config.php';
require_once '../includes/header.php';

// Initialize an array for validation errors and a variable for success message
$errors = [];
$success = '';

// Handle form submission for adding a new category
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Trim removes any extra whitespace around the category name
    $name = trim($_POST['name']);

    // Checks if the input is empty
    if (empty($name)) {
        $errors[] = 'Category name cannot be empty.';
    } else {
        // Prepare an SQL statement to prevent SQL injection
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (:name)");
        // Execute the statement with the user input
        $stmt->execute([':name' => $name]);
        // Show success message to the user
        $success = 'Category added successfully.';
    }
}

// Handle category deletion via a GET request
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delId = (int) $_GET['delete']; // Ensure ID is an integer
    // Prepare and execute the deletion query
    $pdo->prepare("DELETE FROM categories WHERE id = :id")->execute([':id' => $delId]);
    // Redirect back to the same page after deletion to prevent resubmission
    header("Location: manage.php");
    exit;
}

// Fetch all categories from the database, it's sorted alphabetically
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();
?>

<!-- The Pages Title -->
<h1 class="mb-4">Manage Categories</h1>

<!-- Display success message if available -->
<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<!-- Display error messages if there are any -->
<?php if ($errors): ?>
    <ul class="text-danger">
        <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
    </ul>
<?php endif; ?>

<!-- Form to add a new category -->
<form method="POST" class="mb-4">
    <div class="mb-3">
        <label>New Category:</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Add Category</button>
</form>

<!-- Display category list if available -->
<?php if (!$categories): ?>
    <p>No categories found.</p>
<?php else: ?>
    <table class="table table-bordered">
        <thead>
            <tr><th>ID</th><th>Name</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php foreach ($categories as $cat): ?>
            <tr>
                <!-- Show category ID and name -->
                <td><?= $cat['id'] ?></td>
                <td><?= htmlspecialchars($cat['name']) ?></td>
                <td>
                    <!-- Delete button with confirmation prompt -->
                    <a href="manage.php?delete=<?= $cat['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<!-- Include the footer -->
<?php require_once '../includes/footer.php'; ?>

