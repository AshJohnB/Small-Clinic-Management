<?php
/**
 * public/register.php
 */
session_start();
include '../config/connection.php'; // DB connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username         = trim($_POST['username'] ?? '');
    $password         = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if username is taken
        $checkSql  = "SELECT id FROM users WHERE username = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param('s', $username);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $error = "That username is already in use. Please choose another.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insertSql  = "INSERT INTO users (username, password) VALUES (?, ?)";
            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->bind_param('ss', $username, $hashed_password);

            if ($insertStmt->execute()) {
                $success = "Account created successfully! You can now login.";
            } else {
                $error = "Error: " . $insertStmt->error;
            }
            $insertStmt->close();
        }
        $checkStmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta 
    name="viewport" 
    content="width=device-width, initial-scale=1.0"
  />
  <title>Hanni's Clinic - Register</title>

  <!-- Bootstrap 5 -->
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" 
    rel="stylesheet"
  >
  <!-- Font Awesome -->
  <link 
    rel="stylesheet" 
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
  >

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    /* Use the same gradient for consistency */
    body {
      margin: 0;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      background: linear-gradient(135deg, #92FFC0, #002661);
    }

    .auth-card {
      width: 100%;
      max-width: 420px;
      border: none;
      border-radius: 1rem;
      box-shadow: 0 8px 30px rgba(0,0,0,0.1);
      background-color: #fff;
      overflow: hidden;
    }

    .auth-card-header {
      background-color: #0275d8;
      color: #fff;
      padding: 1.5rem;
      text-align: center;
    }

    .auth-card-body {
      padding: 2rem;
    }

    .btn-block {
      width: 100%;
      border-radius: 30px;
      padding: 0.6rem;
      font-weight: 600;
    }

    .footer-text {
      color: #fff;
      margin-top: 1rem;
      text-align: center;
      font-weight: 500;
    }
  </style>
</head>
<body>

  <div class="card auth-card">
    <div class="auth-card-header">
      <h4 class="mb-0"><i class="fas fa-user-plus"></i> Create an Account</h4>
    </div>
    <div class="auth-card-body">
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger text-center"><?php echo $error; ?></div>
      <?php elseif (!empty($success)): ?>
        <div class="alert alert-success text-center"><?php echo $success; ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="mb-3">
          <label for="username" class="fw-bold">Username</label>
          <input 
            type="text" 
            name="username" 
            class="form-control" 
            placeholder="Enter username" 
            required
          >
        </div>
        <div class="mb-3">
          <label for="password" class="fw-bold">Password</label>
          <input 
            type="password" 
            name="password" 
            class="form-control" 
            placeholder="Enter password" 
            required
          >
        </div>
        <div class="mb-4">
          <label for="confirm_password" class="fw-bold">Confirm Password</label>
          <input 
            type="password" 
            name="confirm_password" 
            class="form-control" 
            placeholder="Confirm password" 
            required
          >
        </div>

        <button 
          type="submit" 
          class="btn btn-success btn-block"
        >
          <i class="fas fa-user-plus"></i> Register
        </button>
      </form>

      <div class="text-center mt-3">
        <a href="login.php" class="text-decoration-none text-primary">
          Already have an account? Login here.
        </a>
      </div>
    </div>
  </div>

  <div class="footer-text">
    &copy; 2024 Hanni's Clinic
  </div>

  <!-- JS -->
  <script 
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
  </script>
</body>
</html>
