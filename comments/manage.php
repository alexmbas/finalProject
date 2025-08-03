<?php
// Ensure the user is authenticated and has admin privileges, super important for sites editing process.
require_once '../users/auth.php';
requireAdmin();

// Include database configuration and HTML header
require_once '../db/config.php';
require_once '../includes/header.php';

//Handle comment deletion if a valid ID is passed via the URL
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delId = (int) $_GET['delete']; // Cast ID to integer for safety
    // Prepare and execute the delete query
    $pdo->prepare("DELETE FROM comments WHERE id = :id")->execute([':id' => $delId]);
    // Redirect back to the page to reflect changes and prevent duplicate actions
    header("Location: manage.php");
    exit;
}

// Handle disemvoweling, we're removing all vowels from a comment
if (isset($_GET['disemvowel']) && is_numeric($_GET['disemvowel'])) {
    $disId = (int) $_GET['disemvowel']; // Cast ID to integer
    // Fetch the comment text from the database
    $stmt = $pdo->prepare("SELECT comment FROM comments WHERE id = :id");
    $stmt->execute([':id' => $disId]);
    $comment = $stmt->fetchColumn();

    if ($comment !== false) {
        // Remove all vowels (upper and lowercase) using regex
        $disemvoweled = preg_replace('/[aeiouAEIOU]/', '', $comment);
        // Update the comment in the database with its disemvoweled version
        $update = $pdo->prepare("UPDATE comments SET comment = :c WHERE id = :id");
        $update->execute([':c' => $disemvoweled, ':id' => $disId]);
    }
    // Redirect after updating to avoid resubmitting the action
    header("Location: manage.php");
    exit;
}

// Fetch all comments along with the technique they belong to
$stmt = $pdo->query("SELECT c.id, c.username, c.comment, c.created_at, t.name AS technique
                     FROM comments c
                     JOIN techniques t ON c.technique_id = t.id
                     ORDER BY c.created_at DESC");
$comments = $stmt->fetchAll();
?>

<!-- Page Title -->
<h1 class="mb-4">Manage Comments</h1>

<!-- Show message if no comments exist -->
<?php if (empty($comments)): ?>
    <p>No comments found.</p>
<?php else: ?>
    <!-- Display comments in a table -->
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>Technique</th>
            <th>Username</th>
            <th>Comment</th>
            <th>Created</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($comments as $c): ?>
            <tr>
                <!-- Safely output each comment's data -->
                <td><?= htmlspecialchars($c['technique']) ?></td>
                <td><?= htmlspecialchars($c['username']) ?></td>
                <td><?= htmlspecialchars($c['comment']) ?></td>
                <td><?= $c['created_at'] ?></td>
                <td>
                    <!-- Delete button with a confirmation prompt -->
                    <a href="?delete=<?= $c['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this comment?')">Delete</a>
                    <!-- Disemvowel button to strip vowels from the comment -->
                    <a href="?disemvowel=<?= $c['id'] ?>" class="btn btn-sm btn-warning">Disemvowel</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
