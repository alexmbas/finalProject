<?php
require_once '../users/auth.php';
require_once '../db/config.php';
requireAdmin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid technique ID.');
}

$id = (int) $_GET['id'];

// Getting the image path
$stmt = $pdo->prepare("SELECT image FROM techniques WHERE id = :id");
$stmt->execute([':id' => $id]);
$tech = $stmt->fetch();

if ($tech && !empty($tech['image'])) {
    $imagePath = "../" . $tech['image'];
    if (file_exists($imagePath)) {
        unlink($imagePath); // I'm deleting the image here from our folder
    }
}

// Deleting our Jiu Jitsu techniques from the Database!
$deleteStmt = $pdo->prepare("DELETE FROM techniques WHERE id = :id");
$deleteStmt->execute([':id' => $id]);

header("Location: /pages/list.php");
exit;
?>
