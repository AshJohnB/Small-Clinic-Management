<?php
session_start();

// If you want to restrict homepage to logged-in users, keep this check:
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Otherwise, remove it if the homepage is public.

include '../templates/header.php';
?>

<div class="container mt-5">
    <h1>Welcome to Hanni's Clinic</h1>
    <p>This is the homepage.</p>
</div>

<?php include '../templates/footer.php'; ?>
