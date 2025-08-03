<?php
// Requires the user to be logged in as an admin
require_once '../users/auth.php';
requireAdmin();

// Include database configuration and layout header
require_once '../db/config.php';
require_once '../includes/header.php';

// Function to generate a URL friendly slug from technique names, this was weird.
function makeSlug($string) {
    $slug = preg_replace('/[^a-zA-Z0-9]+/', '-', $string); // Replace non-alphanumeric characters with dashes.
    $slug = trim($slug, '-'); // Remove any leading or trailing dashes.. had to do this as the names were appearing cut off.
    return strtolower($slug); // Convert to lowercase
}

// Initialize filter and pagination variables
$searchTerm = $_GET['search'] ?? '';
$selectedCategoryId = $_GET['category_id'] ?? '';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 5; // Results per page, though of 10, but five is more clean.
$offset = ($page - 1) * $limit;

// Fetch all categories for the category filter dropdown
$catStmt = $pdo->query("SELECT id,name FROM categories ORDER BY name ASC");
$categories = $catStmt->fetchAll();

// Build dynamic WHERE clause based on filters
$where = "WHERE 1=1";
$params = [];

if (!empty($searchTerm)) {
    $where .= " AND (t.name LIKE :search1 OR t.description LIKE :search2)";
    $params[':search1'] = "%$searchTerm%";
    $params[':search2'] = "%$searchTerm%";
}

if (!empty($selectedCategoryId) && is_numeric($selectedCategoryId)) {
    $where .= " AND t.category_id = :category_id";
    $params[':category_id'] = $selectedCategoryId;
}

// Count the total number of results for pagination
$countSql = "SELECT COUNT(*) FROM techniques t $where";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalResults = $countStmt->fetchColumn();
$totalPages = ceil($totalResults / $limit);

// Fetch the current page of techniques with optional filters applied
$sql = "SELECT t.*, c.name AS category_name 
        FROM techniques t 
        LEFT JOIN categories c ON t.category_id=c.id 
        $where 
        ORDER BY t.created_at DESC 
        LIMIT :limit OFFSET :offset";

// Add limit and offset to params
$params[':limit'] = $limit;
$params[':offset'] = $offset;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$techniques = $stmt->fetchAll();
?>

<h1 class="mb-4">BJJ Techniques</h1>

<!-- Search and category filter form -->
<form method="GET" class="row g-3 mb-4">
  <div class="col-md-4">
    <input type="text" class="form-control" name="search" placeholder="Search techniques..." value="<?= htmlspecialchars($searchTerm) ?>">
  </div>
  <div class="col-md-4">
    <select name="category_id" class="form-select" onchange="this.form.submit()">
      <option value="">-- All Categories --</option>
      <?php foreach ($categories as $cat): ?>
        <option value="<?= $cat['id'] ?>" <?= ($cat['id']==$selectedCategoryId)?'selected':''; ?>>
          <?= htmlspecialchars($cat['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <!-- Fallback button if JavaScript is disabled -->
  <noscript><div class="col-md-2"><button class="btn btn-primary">Apply</button></div></noscript>
</form>

<?php if (!$techniques): ?>
  <!-- Message shown when no techniques are found -->
  <p>No techniques found.</p>
<?php else: ?>
  <!-- Table of techniques -->
  <table class="table table-bordered">
    <thead>
      <tr><th>Name</th><th>Type</th><th>Belt</th><th>Category</th><th>Image</th><th>Created</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <?php foreach ($techniques as $tech): ?>
      <tr>
        <td><?= htmlspecialchars($tech['name']) ?></td>
        <td><?= htmlspecialchars($tech['type']) ?></td>
        <td><?= htmlspecialchars($tech['belt_level']) ?></td>
        <td><?= htmlspecialchars($tech['category_name'] ?? 'Uncategorized') ?></td>
        <td>
          <?php if (!empty($tech['image'])): ?>
            <img src="../<?= htmlspecialchars($tech['image']) ?>" width="80">
          <?php else: ?>
            No image
          <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($tech['created_at']) ?></td>
        <td>
          <!-- Action buttons; view, edit, delete -->
          <a href="/technique/<?= $tech['id'] ?>-<?= makeSlug($tech['name']) ?>" class="btn btn-sm btn-info">View</a>
          <a href="edit.php?id=<?= $tech['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
          <a href="delete.php?id=<?= $tech['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <!-- Pagination controls -->
  <p>Page <?= $page ?> of <?= $totalPages ?></p>
  <div>
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <a href="?search=<?= urlencode($searchTerm) ?>&category_id=<?= urlencode($selectedCategoryId) ?>&page=<?= $i ?>">
        <?= ($i == $page) ? "<strong>$i</strong>" : $i; ?>
      </a>
    <?php endfor; ?>
  </div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
