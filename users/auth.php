<?php
// users/auth.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

function isLoggedIn(): bool {
  return !empty($_SESSION['user_id']);
}

function isAdmin(): bool {
  //cast to int for safety
  return !empty($_SESSION['is_admin']) && (int)$_SESSION['is_admin'] === 1;
}
 // Gate for any logged-in user.If not logged in, go to the real login file.
function requireLogin(): void {
  if (!isLoggedIn()) {
    header('Location: /users/login.php');
    exit;
  }
}
 // Gate for admins only.If not logged in, then login.If logged in but not admin? Then sending my user somewhere safe that they can view the page, such as /pages/list.php
function requireAdmin(): void {
  if (!isLoggedIn()) {
    header('Location: /users/login.php');
    exit;
  }
  if (!isAdmin()) {
    header('Location: /pages/list.php');
    exit;
  }
}
