<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta 
    name="viewport" 
    content="width=device-width, initial-scale=1.0" 
  />
  <title>Hanni's Clinic</title>
  
  <!-- Bootstrap 5 CSS -->
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" 
    rel="stylesheet"
  />
  
  <!-- Font Awesome -->
  <link 
    rel="stylesheet" 
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
  />

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body style="background-color: #f4f6f9;">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <!-- If your homepage is public/index.php -->
    <a class="navbar-brand" href="../public/index.php">Hanni's Clinic</a>
    <button 
      class="navbar-toggler" 
      type="button" 
      data-bs-toggle="collapse" 
      data-bs-target="#navbarNav" 
      aria-controls="navbarNav" 
      aria-expanded="false" 
      aria-label="Toggle navigation"
    >
      <span class="navbar-toggler-icon"></span>
    </button>
    <div 
      class="collapse navbar-collapse justify-content-end" 
      id="navbarNav"
    >
      <ul class="navbar-nav">
        <li class="nav-item">
          <!-- Link to patients/view.php -->
          <a class="nav-link" href="../patients/view.php">
            <i class="fas fa-users"></i> Patients
          </a>
        </li>
        <li class="nav-item">
          <!-- Link to logout.php (in public/) -->
          <a class="nav-link" href="../public/logout.php">
            <i class="fas fa-sign-out-alt"></i> Logout
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
