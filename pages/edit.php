<?php
// Enable error reporting and output buffering to prevent header issues
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();

//Require user to be authenticated and have admin privileges.
require_once __DIR__ . '/../users/auth.php';
requireAdmin();

// Load database configuration and page layout.
require_once __DIR__ . '/../db/config.php';
require_once __DIR__ . '/../includes/header.php';

// Loading Composer autoload and image library.
require_once __DIR__ . '/../vendor/autoload.php';

use Intervention\Image\ImageManagerStatic as Image;
Image::configure(['driver' => 'gd']);

// Include TinyMCE editor for the description textarea. API was giving me grief, figured it out though.
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
    form.addEventListener('submit', function () {
      tinymce.triggerSave();
    });
  });
</script>
EOT;

// Validate that a technique ID was passed in the URL.
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: /pages/list.php");
    exit;
}

$id = (int) $_GET['id'];

// Fetch the technique data from the database
$stmt = $pdo->prepare("SELECT * FROM techniques WHERE id = :id");
$stmt->execute([':id' => $id]);
$tech = $stmt->fetch();

// If no technique is found, display a message and stop processing.
if (!$tech) {
    echo "<p>Technique not found.</p>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Pre-fill form fields with current data.
$errors = [];
$name = $tech['name'];
$type = $tech['type'];
$belt_level = $tech['belt_level'];
$description = $tech['description'];
$category_id = $tech['category_id'];

// Fetch category list for dropdown
$catStmt = $pdo->query("SELECT id,name FROM categories ORDER BY name ASC");
$categories = $catStmt->fetchAll();

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //Get user-submitted data
    $name = trim($_POST['name']);
    $type = trim($_POST['type']);
    $belt_level = trim($_POST['belt_level']);
    $description = $_POST['description']; // HTML is allowed here
    $category_id = $_POST['category_id'];

    //Check if all required fields are filled
    if (!$name || !$type || !$belt_level || !$description || !$category_id) {
        $errors[] = 'All fields are required.';
    }

    $image_path = $tech['image']; // Keep the existing image path unless a new one is uploaded

    // Check if a new image was uploaded
    if (!empty($_FILES['image']['tmp_name'])) {
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check === false) {
            $errors[] = 'Uploaded file must be an image.';
        } else {
            try {
                // Resize and optimize the new image
                $img = Image::make($_FILES['image']['tmp_name'])
                    ->resize(800, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });

                //Generate unique filename for the new image
                $basename = 'assets/images/uploads/' . uniqid() . '.jpg';
                $img->save(__DIR__ . '/../' . $basename, 75);
                $image_path = $basename;

                //Remove the old image file if it exists
                if (!empty($tech['image']) && file_exists(__DIR__ . '/../' . $tech['image'])) {
                    unlink(__DIR__ . '/../' . $tech['image']);
                }
            } catch (Exception $e) {
                $errors[] = 'Image processing failed: ' . $e->getMessage();
            }
        }
    }

    // If there are no errors, update the technique in the database
    if (!$errors) {
        $stmt = $pdo->prepare("UPDATE techniques SET name=:name, type=:type, belt_level=:belt, description=:desc, category_id=:cat, image=:img WHERE id=:id");
        $stmt->execute([
            ':name' => $name,
            ':type' => $type,
            ':belt' => $belt_level,
            ':desc' => $description,
            ':cat' => $category_id,
            ':img' => $image_path,
            ':id' => $id
        ]);
        header("Location: /pages/view.php?id=" . $id); // <- updated line for Heroku
        exit;
    }
}
?>

<h1 class="mb-4">Edit Technique</h1>

<?php if ($errors): ?>
    <ul class="text-danger">
        <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
    </ul>
<?php endif; ?>

<!-- Form to update technique details -->
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
        <!-- Changed to dropdown for belt levels -->
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
        <label>Change Image (optional)</label>
        <input class="form-control" type="file" name="image" accept="image/*">
        <?php if (!empty($tech['image'])): ?>
            <?php $imgSrc = '/' . ltrim($tech['image'], '/'); ?>
            <p class="mt-2">Current: <img src="<?= htmlspecialchars($imgSrc) ?>" width="100"></p>
        <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-warning">Update Technique</button>
</form>

<?php
require_once __DIR__ . '/../includes/footer.php';
ob_end_flush(); // Flush buffer to allow safe header redirection
?>
