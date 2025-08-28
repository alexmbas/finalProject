<?php
require_once __DIR__ . '/../users/auth.php';
requireAdmin();

// Include the database connection and layout header
require_once __DIR__ . '/../db/config.php';
require_once __DIR__ . '/../includes/header.php';

// Initialize variables for form inputs and error tracking
$errors = [];
$username = '';
$is_admin = 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize form inputs
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    // Validate inputs
    if ($username === '' || $password === '') {
        $errors[] = 'All fields are required.';
    } elseif ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    } else {
        // Check if the username already exists
        $check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :u");
        $check->execute([':u' => $username]);
        if ($check->fetchColumn() > 0) {
            $errors[] = 'Username already exists.';
        } else {
            // Hash the password securely
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // Insert the new user into the database
            $stmt = $pdo->prepare("INSERT INTO users (username, password, is_admin) VALUES (:u, :p, :a)");
            $stmt->execute([
                ':u' => $username,
                ':p' => $hash,
                ':a' => $is_admin
            ]);

            // Redirect back to the user management page
            header("Location: manage.php");
            exit;
        }
    }
}
?>

<h1 class="mb-4">Add New User</h1>

<!-- Display validation errors if any exist -->
<?php if ($errors): ?>
    <ul class="text-danger">
        <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
    </ul>
<?php endif; ?>

<!-- User created form -->
<form method="POST" class="mb-5">
    <div class="mb-3">
        <label>Username (email):</label>
        <input type="email" name="username" value="<?= htmlspecialchars($username) ?>" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Password:</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Confirm Password:</label>
        <input type="password" name="confirm" class="form-control" required>
    </div>
    <div class="form-check mb-3">
        <input type="checkbox" name="is_admin" class="form-check-input" id="adminCheck" <?= $is_admin ? 'checked' : '' ?>>
        <label class="form-check-label" for="adminCheck">Make Admin</label>
    </div>
    <button type="submit" class="btn btn-success">Create User</button>
</form>

<p><a href="/users">← Back to User List</a></p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
