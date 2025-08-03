<?php
require_once 'auth.php';
requireAdmin();

require_once '../db/config.php';
require_once '../includes/header.php';

// Validate the user ID passed via GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage.php");
    exit;
}

$id = (int) $_GET['id'];

// Fetch user details from the database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $id]);
$user = $stmt->fetch();

// Show an error if user is not found
if (!$user) {
    echo "<p>User not found.</p>";
    require_once '../includes/footer.php';
    exit;
}

//Initialize form variables
$errors = [];
$username = $user['username'];
$is_admin = $user['is_admin'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    // Validate inputs
    if ($username === '') {
        $errors[] = 'Username cannot be empty.';
    } elseif ($password !== '' && $password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    } else {
        // Update username and admin status
        $stmt = $pdo->prepare("UPDATE users SET username = :u, is_admin = :a WHERE id = :id");
        $stmt->execute([':u' => $username, ':a' => $is_admin, ':id' => $id]);

        // Update password if one was provided.
        if ($password !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET password = :p WHERE id = :id")->execute([':p' => $hash, ':id' => $id]);
        }

        // Redirect to user management page
        header("Location: manage.php");
        exit;
    }
}
?>

<h1 class="mb-4">Edit User</h1>

<?php if ($errors): ?>
    <ul class="text-danger">
        <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="POST" class="mb-5">
    <div class="mb-3">
        <label>Username (email):</label>
        <input type="email" name="username" value="<?= htmlspecialchars($username) ?>" class="form-control" required>
    </div>

    <div class="form-check mb-3">
        <input type="checkbox" name="is_admin" class="form-check-input" id="adminCheck" <?= $is_admin ? 'checked' : '' ?>>
        <label class="form-check-label" for="adminCheck">Make Admin</label>
    </div>

    <div class="mb-3">
        <label>New Password (leave blank to keep current):</label>
        <input type="password" name="password" class="form-control">
    </div>

    <div class="mb-3">
        <label>Confirm Password:</label>
        <input type="password" name="confirm" class="form-control">
    </div>

    <button type="submit" class="btn btn-primary">Update User</button>
</form>

<p><a href="manage.php">← Back to User List</a></p>

<?php require_once '../includes/footer.php'; ?>
