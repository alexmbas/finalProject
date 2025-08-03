<?php
// Load Composer dependencies, including dotnev.
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables from the .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Get the API base URL from the environment.
$apiBase = rtrim($_ENV['JIUJITERO_API'], '/');

// Set pagination values
$limit = 10; // Number of results per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Build the full API URL with pagination parameters
$url = "$apiBase/users?limit=$limit&offset=$offset";

// Initialize a cURL session to call the API, ultimately didn't work. Bummer.
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,      // Return the result instead of outputting it
    CURLOPT_CONNECTTIMEOUT => 5,         // Wait 5 seconds for a connection - had to do this or else I got an infinite loop.
    CURLOPT_TIMEOUT => 10                // Max time for full execution
]);
$response = curl_exec($ch);              // Execute the request
$curlErr = curl_error($ch);              // Capture any error message
curl_close($ch);                         // Close the session

// Process the API response
if ($response === false) {
    $error = "API request failed: " . ($curlErr ?: 'Unknown error');
} else {
    $json = json_decode($response, true); // Convert JSON to an associative array
    if (!$json || !is_array($json)) {
        $error = "Invalid response format.";
    } else {
        // Use 'data' if present, otherwise assume the response is the user list
        $users = $json['data'] ?? $json;
    }
}

// Include the page header layout for my css styles.
require_once __DIR__ . '/../includes/header.php';
?>

<h1 class="mb-4">Jiu Jitero Athletes</h1>

<?php if (!empty($error)): ?>
  <!-- Display error message if something went wrong -->
  <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php elseif (empty($users)): ?>
  <!-- Show a fallback message if no users were returned -->
  <p>No athletes found.</p>
<?php else: ?>
  <!-- Display the list of athletes -->
  <ul class="list-group mb-4">
    <?php foreach ($users as $u): ?>
      <li class="list-group-item">
        <strong><?= htmlspecialchars($u['name'] ?? 'Unknown') ?></strong><br>
        Academy: <?= htmlspecialchars($u['academy'] ?? 'Unknown') ?>
      </li>
    <?php endforeach; ?>
  </ul>

  <!-- Basic pagination controls -->
  <nav>
    <?php if ($page > 1): ?>
      <a href="?page=<?= $page-1 ?>" class="btn btn-secondary">&laquo; Prev</a>
    <?php endif; ?>
    <a href="?page=<?= $page+1 ?>" class="btn btn-secondary">Next &raquo;</a>
  </nav>
<?php endif; ?>

<!-- Include the footer layout -->
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
