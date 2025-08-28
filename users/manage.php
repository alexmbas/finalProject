<?php
require_once __DIR__ . '/../users/auth.php';
requireAdmin();

require_once __DIR__ . '/../db/config.php';
require_once __DIR__ . '/../includes/header.php';

// Handle user deletion if a valid user ID is provided
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    // Delete the user with the given ID
    $pdo->prepare("DELETE FROM users WHERE id = :id")->execute([':id' => $id]);

    header("Location: manage.php");
    exit;
}

// Retrieve all users from the database, sorted by most recent
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<h1 class="mb-4">Manage Users</h1>

<!-- Link to the add user form -->
<p><a href="add.php" class="btn btn-success">➕ Add New User</a></p>

<!-- Show message if no users are found -->
<?php if (empty($users)): ?>
    <p>No users found.</p>
<?php else: ?>
    <!-- Display user data in a table -->
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>Username</th>
            <th>Is Admin</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['username']) ?></td>
                <td><?= $u['is_admin'] ? 'Yes' : 'No' ?></td>
                <td><?= $u['created_at'] ?></td>
                <td>
                    <!-- Edit user link -->
                    <a href="edit.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-warning">Edit</a>

                    <!-- Prevent deleting yourself! That wouldn't be fun. -->
                    <?php if (!isset($_SESSION['user_id']) || $u['id'] !== $_SESSION['user_id']): ?>
                        <a href="?delete=<?= $u['id'] ?>" class="btn btn-sm btn-danger"
                           onclick="return confirm('Delete this user?')">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
