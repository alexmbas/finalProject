<?php
// Start the session to manage CAPTCHA and user data
session_start();

// Include database configuration and layout header
require_once __DIR__ . '/../db/config.php';
require_once __DIR__ . '/../includes/header.php';

// Validating the technique ID passed via GET with IF.
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Invalid Technique ID.</p>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$id = (int) $_GET['id'];

// Fetch technique details from the database
$stmt = $pdo->prepare("SELECT t.*, c.name AS category_name FROM techniques t LEFT JOIN categories c ON t.category_id = c.id WHERE t.id = :id");
$stmt->execute([':id' => $id]);
$technique = $stmt->fetch();

// Show error and exit if technique does not exist
if (!$technique) {
    echo "<p>Technique not found.</p>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Initialize error message for comment form
$commentError = '';

// Handle new comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['username'], $_POST['comment'], $_POST['captcha'])) {

    $username = trim($_POST['username']);
    $comment = trim($_POST['comment']);
    $captcha = strtoupper(trim($_POST['captcha']));

    // Validate input fields
    if ($username === '' || $comment === '') {
        $commentError = 'Username and comment cannot be empty.';
    } elseif (!isset($_SESSION['captcha']) || $captcha !== $_SESSION['captcha']) {
        $commentError = 'Invalid CAPTCHA. Please try again.';
    } else {
        // Insert the comment into the database
        $insert = $pdo->prepare("INSERT INTO comments (technique_id, username, comment) VALUES (:tech_id, :username, :comment)");
        $insert->execute([
            ':tech_id' => $id,
            ':username' => $username,
            ':comment' => $comment
        ]);

        // Clear CAPTCHA after successful submission
        unset($_SESSION['captcha']);

        // Redirect to avoid form resubmission
        header("Location: view.php?id=" . $id);
        exit;
    }
}

// Fetch all comments related to my technique
$comStmt = $pdo->prepare("SELECT * FROM comments WHERE technique_id = :id ORDER BY created_at DESC");
$comStmt->execute([':id' => $id]);
$comments = $comStmt->fetchAll();
?>

<h1 class="mb-4"><?= htmlspecialchars($technique['name']) ?></h1>

<!-- Display technique details -->
<p><strong>Type:</strong> <?= htmlspecialchars($technique['type']) ?></p>
<p><strong>Belt Level:</strong> <?= htmlspecialchars($technique['belt_level']) ?></p>
<p><strong>Category:</strong> <?= htmlspecialchars($technique['category_name'] ?? 'Uncategorized') ?></p>
<p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($technique['description'])) ?></p>
<p><strong>Created At:</strong> <?= htmlspecialchars($technique['created_at']) ?></p>

<!-- Show technique image if available -->
<?php if (!empty($technique['image'])): ?>
    <p><strong>Technique Image:</strong><br>
        <img src="/<?= htmlspecialchars($technique['image']) ?>" width="400" class="img-fluid">
    </p>
<?php endif; ?>

<hr>
<h2>Comments</h2>

<!-- Show existing comments or message if none exist -->
<?php if (count($comments) === 0): ?>
    <p>No comments yet. Be the first to comment!</p>
<?php else: ?>
    <?php foreach ($comments as $c): ?>
        <div class="border p-3 mb-2">
            <strong><?= htmlspecialchars($c['username']) ?></strong>
            <small class="text-muted">(<?= $c['created_at'] ?>)</small>
            <p><?= nl2br(htmlspecialchars($c['comment'])) ?></p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<hr>
<h3>Add a Comment</h3>

<!-- Show validation error if applicable -->
<?php if ($commentError): ?>
    <p class="text-danger"><?= htmlspecialchars($commentError) ?></p>
<?php endif; ?>

<!-- Comment submission form -->
<form method="POST" class="mb-5">
    <div class="mb-3">
        <label>Your Name:</label>
        <input type="text" name="username" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Your Comment:</label>
        <textarea name="comment" rows="5" class="form-control" required></textarea>
    </div>

    <div class="mb-3">
        <label>CAPTCHA:</label><br>
        <img src="/captcha.php" alt="CAPTCHA Image"><br><br>
        <input type="text" name="captcha" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Submit Comment</button>
</form>

<p><a href="/techniques">← Back to List</a></p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
