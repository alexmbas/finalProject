<?php
session_start();

require_once '../db/config.php';
require_once '../includes/header.php'; 

$username = $password = '';
$loginError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if ($username === '' || $password === '') {
        $loginError = 'Both fields are required.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'];

            header("Location: ../pages/list.php");
            exit;
        } else {
            $loginError = 'Invalid username or password.';
        }
    }
}
?>

<h1 class="mb-4">Login</h1>

<?php if (isset($_GET['registered'])): ?>
    <p class="text-success">Registration successful. Please log in.</p>
<?php endif; ?>

<?php if ($loginError): ?>
    <p class="text-danger"><?= htmlspecialchars($loginError) ?></p>
<?php endif; ?>

<form method="POST" class="mb-5">
    <div class="mb-3">
        <label>Username:</label>
        <input type="text" name="username" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Password:</label>
        <input type="password" name="password" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Login</button>
</form>

<p><a href="../users/users.php">Need an account?</a></p>
<p><a href="../pages/list.php">← Back to Techniques</a></p>

<?php require_once '../includes/footer.php'; ?>
