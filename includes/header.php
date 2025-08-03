<?php 
// Start a new session if one hasn't been started yet
if (session_status() === PHP_SESSION_NONE) session_start(); 
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>BJJ VAULT</title>

  <!-- Bootstrap CSS for styling and layout! -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    /* General body styling */
    body { background: #0b001c; color: #e0e0ff; }

    /* Navbar gradient and link styling */
    nav { background: linear-gradient(45deg, #2b0057, #8200ff); }
    .navbar-brand, .nav-link, .navbar-text {
      color: #ff77ff !important;
      text-shadow: 0 0 6px #ff77ff;
    }

    /* Button color that overrides the Bootstrap buttons */
    a.btn-info { background-color: #ff5ec7; border-color: #ff5ec7; }
    a.btn-warning { background-color: #ffa500; border-color: #ffa500; }
    a.btn-danger { background-color: #ff1a1a; border-color: #ff1a1a; }

    /* Table styling to match the retro and synthwave aesthetics */
    table { background: #1a0033; color: #fff; }
    th, td { border-color: #440060; }

    /* Hidden Konami Easter egg image style */
    #konami-img {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      width: 300px;
      transform: translate(-50%, -50%);
      z-index: 9999;
      border: 5px solid #ff77ff;
      box-shadow: 0 0 30px #ff77ff;
      border-radius: 15px;
      animation: pulse 2s infinite;
    }

    /* Glowing animation for Konami image, this was fun. */
    @keyframes pulse {
      0% { box-shadow: 0 0 20px #ff77ff; }
      50% { box-shadow: 0 0 40px #ff77ff; }
      100% { box-shadow: 0 0 20px #ff77ff; }
    }
  </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg mb-4">
  <div class="container">
    <!-- Logo and Home Link -->
    <a class="navbar-brand" href="/WebDev2/finalProject/pages/list.php">BJJ VAULT</a>
    
    <!-- Left side navigation links -->
    <ul class="navbar-nav me-auto">
      <li class="nav-item"><a class="nav-link" href="/WebDev2/finalProject/pages/create.php">Add Technique</a></li>
      <li class="nav-item"><a class="nav-link" href="/WebDev2/finalProject/categories/manage.php">Categories</a></li>
      <li class="nav-item"><a class="nav-link" href="/WebDev2/finalProject/users/manage.php">Users</a></li>
      <li class="nav-item"><a class="nav-link" href="/WebDev2/finalProject/comments/manage.php">Comments</a></li>
    </ul>

    <!-- Rightside user session controls -->
    <ul class="navbar-nav">
      <?php if (!empty($_SESSION['user_id'])): ?>
        <!-- Greet the logged-in user -->
        <li class="nav-item"><span class="navbar-text">Hi, <?= htmlspecialchars($_SESSION['username']) ?></span></li>
        <li class="nav-item"><a class="nav-link" href="/WebDev2/finalProject/users/logout.php">Logout</a></li>
      <?php else: ?>
        <li class="nav-item"><a class="nav-link" href="/WebDev2/finalProject/users/login.php">Login</a></li>
      <?php endif; ?>
    </ul>
  </div>
</nav>

<!-- Synthwave Easter Egg...Hidden image triggered by Konami code. Cat Attack! -->
<img id="konami-img" src="https://media.giphy.com/media/LmNwrBhejkK9EFP504/giphy.gif" alt="Synthwave Surprise" />

<!-- JavaScript to detect Konami code and trigger Easter egg -->
<script>
  let input = '';
  const code = '38384040373937396665'; // up up down down left right left right b a

  document.addEventListener('keydown', (e) => {
    input += e.keyCode;
    // If the sequence ends with the Konami code, show the image for 10 seconds, then image will fade away.
    if (input.endsWith(code)) {
      const img = document.getElementById('konami-img');
      img.style.display = 'block';
      setTimeout(() => { img.style.display = 'none'; }, 10000);
    }
  });
</script>

<!-- Container wrapper for main page content -->
<div class="container">
