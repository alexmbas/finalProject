<?php
// Start the session to store login information
session_start();

require_once '../db/config.php';
require_once '../includes/header.php'; 

// Initialize variables for form input and error handling
$username = $password = '';
$loginError = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Check for empty fields
    if ($username === '' || $password === '') {
        $loginError = 'Both fields are required.';
    } else {
        // Fetch user from the database based on submitted username
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        // Verify that the user exists and the password matches
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'];

            // Redirect to main techniques page after login
            header("Location: ../pages/list.php");
            exit;
        } else {
            //Generic error for invalid login
            $loginError = 'Invalid username or password.';
        }
    }
}
?>

<h1 class="mb-4">Login</h1>

<!-- Display a success message if redirected after registration -->
<?php if (isset($_GET['registered'])): ?>
    <p class="text-success">Registration successful. Please log in.</p>
<?php endif; ?>

<!-- Show login error message if login fails -->
<?php if ($loginError): ?>
    <p class="text-danger"><?= htmlspecialchars($loginError) ?></p>
<?php endif; ?>

<!-- Login form -->
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

<!-- Navigation links for users, *REMEMBER* need a registration page -->
<p><a href="../users/users.php">Need an account?</a></p>
<p><a href="../pages/list.php">← Back to Techniques</a></p>

<?php require_once '../includes/footer.php'; ?>
