<?php
// Enable full error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start(); // Start output buffering to prevent 'headers already sent' warning

// Ensure the user is authenticated and has admin permissions, super important!
require_once '../users/auth.php';
requireAdmin();

// Include database connection and page header.
require_once '../db/config.php';
require_once '../includes/header.php';

// Load dependencies, including the Intervention Image library for image processing.
require_once __DIR__ . '/../vendor/autoload.php';

use Intervention\Image\ImageManagerStatic as Image;
Image::configure(['driver' => 'gd']);

// Load TinyMCE WYSIWYG editor for the description field.
echo <<<EOT
<script src="https://cdn.tiny.cloud/1/jafm6iohdfejz973wyxymh0h4xfhwaev8pt02axi7xvzp2t4/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  tinymce.init({
    selector: 'textarea#description',
    menubar: false,
    height: 300,
    plugins: 'link lists',
    toolbar: 'undo redo | bold italic underline | bullist numlist | link removeformat',
    content_style: "body { font-family:Helvetica,Arial,sans-serif; font-size:14px }"
  });

  // Make sure TinyMCE updates the textarea before form submission
  document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    form.addEventListener('submit', function (e) {
      tinymce.triggerSave();
      console.log('Form is submitting...'); // DEBUG LOG
    });
  });
</script>
EOT;

// Initialize variables for form inputs and error tracking.
$errors = [];
$name = $type = $belt_level = $description = '';
$category_id = '';

// Fetch category options for the select dropdown.
$catStmt = $pdo->query("SELECT id,name FROM categories ORDER BY name");
$categories = $catStmt->fetchAll();

// Handle form submission.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize form inputs.
    $name = trim($_POST['name']);
    $type = trim($_POST['type']);
    $belt_level = trim($_POST['belt_level']);
    $description = $_POST['description']; // Allow HTML formatting.
    $category_id = $_POST['category_id'];

    // Check for required fields.
    if (!$name || !$type || !$belt_level || !$description || !$category_id) {
        $errors[] = 'All fields are required.';
    }

    $image_path = '';

    // Handle image upload if a file is submitted.
    if (!empty($_FILES['image']['tmp_name'])) {
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check === false) {
            $errors[] = 'Uploaded file must be an image.';
        } else {
            try {
                //Resize and constrain the image using Intervention Image.
                $img = Image::make($_FILES['image']['tmp_name'])
                    ->resize(800, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });

                // Generate a unique filename and save the processed image, this took some time to figure out.
                $basename = 'assets/images/uploads/' . uniqid() . '.jpg';
                $img->save(__DIR__ . '/../' . $basename, 75);
                $image_path = $basename;
            } catch (Exception $e) {
                $errors[] = 'Image processing failed: ' . $e->getMessage();
            }
        }
    }

    // If there are no validation errors, insert the new technique into the database.
    if (!$errors) {
        try {
            $stmt = $pdo->prepare("INSERT INTO techniques (name, type, belt_level, description, category_id, image) VALUES (:name, :type, :belt, :desc, :cat, :img)");
            $stmt->execute([
                ':name' => $name,
                ':type' => $type,
                ':belt' => $belt_level,
                ':desc' => $description,
                ':cat' => $category_id,
                ':img' => $image_path
            ]);
            header("Location: /pages/list.php");
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}
?>

<h1 class="mb-4">Add New Technique</h1>

<?php if ($errors): ?>
  <ul class="text-danger">
    <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
  </ul>
<?php endif; ?>

<!-- Form for creating a new technique -->
<form method="POST" enctype="multipart/form-data">
  <div class="mb-3">
    <label>Name</label>
    <input class="form-control" type="text" name="name" value="<?= htmlspecialchars($name) ?>" required>
  </div>

  <div class="mb-3">
    <label>Type</label>
    <input class="form-control" type="text" name="type" value="<?= htmlspecialchars($type) ?>" required>
  </div>

  <div class="mb-3">
    <label>Belt Level</label>
    <select class="form-select" name="belt_level" required>
      <option value="">-- Select Belt --</option>
      <?php
        $belts = ['white', 'blue', 'purple', 'brown', 'black'];
        foreach ($belts as $belt) {
          $selected = ($belt === $belt_level) ? 'selected' : '';
          echo "<option value=\"$belt\" $selected>" . ucfirst($belt) . "</option>";
        }
      ?>
    </select>
  </div>

  <div class="mb-3">
    <label>Category</label>
    <select name="category_id" class="form-select" required>
      <option value="">-- Select --</option>
      <?php foreach ($categories as $cat): ?>
        <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $category_id) ? 'selected' : '' ?>>
          <?= htmlspecialchars($cat['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="mb-3">
    <label>Description</label>
    <!-- Removed 'required' to prevent 'not focusable' error with TinyMCE -->
    <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($description) ?></textarea>
  </div>

  <div class="mb-3">
    <label>Image (optional)</label>
    <input class="form-control" type="file" name="image" accept="image/*">
  </div>

  <button type="submit" class="btn btn-primary">Create Technique</button>
</form>

<?php
require_once '../includes/footer.php';
ob_end_flush(); // Flush output buffer to prevent header warning
?>
