<?php
session_start();

require_once __DIR__ . '/../db/config.php';

$username = $password = '';
$loginError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $loginError = 'Both fields are required.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Important: stabilize the session
            session_regenerate_id(true);

            $_SESSION['user_id']  = (int)$user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = (int)$user['is_admin'];

            header("Location: /pages/list.php");
            exit;
        } else {
            $loginError = 'Invalid username or password.';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<h1 class="mb-4">Login</h1>

<?php if (isset($_GET['registered'])): ?>
  <p class="text-success">Registration successful. Please log in.</p>
<?php endif; ?>

<?php if ($loginError): ?>
  <p class="text-danger"><?= htmlspecialchars($loginError) ?></p>
<?php endif; ?>

<form method="POST" class="mb-5" action="/users/login.php">
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

<p><a href="/users/users.php">Need an account?</a></p>
<p><a href="/pages/list.php">← Back to Techniques</a></p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
