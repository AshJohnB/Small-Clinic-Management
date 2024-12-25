<?php
/**
 * public/login.php
 */

session_start();
include '../config/connection.php'; // If needed

// Handle login submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $sql  = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $username;
            header("Location: ../patients/view.php");
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
    $stmt->close();
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
  <title>Hanni's Clinic - Login</title>

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

  <!-- Optional: SweetAlert2, if you want it -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    /* Same gradient for both login & register */
    body {
      margin: 0;  /* remove default body margin */
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      background: linear-gradient(135deg, #92FFC0, #002661);
    }

    .auth-card {
      width: 100%;
      max-width: 400px;
      border: none;
      border-radius: 1rem;
      box-shadow: 0 8px 30px rgba(0,0,0,0.1);
      overflow: hidden;
      background-color: #fff; /* card background */
    }

    .auth-card-header {
      background-color: #333;
      color: #fff;
      padding: 1.5rem;
      text-align: center;
      border-bottom: none;
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
      <h4 class="mb-0">
        <i class="fas fa-user-md"></i> Hanni's Clinic - Login
      </h4>
    </div>
    <div class="auth-card-body">
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger text-center"><?php echo $error; ?></div>
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

        <div class="mb-4">
          <label for="password" class="fw-bold">Password</label>
          <input 
            type="password" 
            name="password" 
            class="form-control" 
            placeholder="Enter password" 
            required
          >
        </div>

        <button 
          type="submit" 
          class="btn btn-success btn-block"
        >
          <i class="fas fa-sign-in-alt"></i> Login
        </button>
      </form>

      <div class="text-center mt-3">
        <a href="register.php" class="text-decoration-none text-primary">
          Don't have an account? Create one here.
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
