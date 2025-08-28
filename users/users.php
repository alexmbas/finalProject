<?php

require_once __DIR__ . '/../db/config.php';
require_once __DIR__ . '/../includes/header.php';

// Initialize form field variables and error tracking
$username = $password = $confirm = '';
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize input values
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    // Validate inputs.
    if ($username === '' || $password === '' || $confirm === '') {
        $errors[] = 'All fields are required.';
    } elseif ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    } else {
        // Check if the username is already in use.
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);

        if ($stmt->fetchColumn() > 0) {
            $errors[] = 'That username is already taken.';
        } else {
            // Hash the password securely, so important**!!
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            // Insert the new user into the database.
            $insert = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
            $insert->execute([':username' => $username, ':password' => $hashed]);

            // Redirect to login page with success indicator.
            header("Location: login.php?registered=1");
            exit;
        }
    }
}
?>

<h1 class="mb-4">Create an Account</h1>

<!-- Display error messages if any -->
<?php if (!empty($errors)): ?>
    <ul class="text-danger">
        <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<!-- Registration form -->
<form method="POST" class="mb-5">
    <div class="mb-3">
        <label>Username (email or name):</label>
        <input type="text" name="username" value="<?= htmlspecialchars($username) ?>" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Password:</label>
        <input type="password" name="password" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Confirm Password:</label>
        <input type="password" name="confirm" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Register</button>
</form>

<!-- Link back to techniques page -->
<p><a href="/pages/list.php">← Back to Techniques</a></p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
