<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>KiddieLearn Login</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <!-- Bootstrap CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <style>
    body {
      background: url('images/bg-login.jpg') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Segoe UI', sans-serif;
    }

    .login-container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      backdrop-filter: blur(5px);
    }

    .login-card {
      width: 100%;
      max-width: 400px;
      padding: 40px;
      background: rgba(255, 255, 255, 0.95);
      border-radius: 20px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    .login-card h3 {
      color: #e91e63;
      font-weight: 700;
      margin-bottom: 25px;
    }

    .login-card .btn {
      background-color: #e91e63;
      border: none;
    }

    .login-card .btn:hover {
      background-color: #d81b60;
    }

    .form-floating label i {
      margin-right: 8px;
      color: #999;
    }

    .login-card .links {
      margin-top: 20px;
      font-size: 14px;
    }

    .login-card .links a {
      color: #e91e63;
      text-decoration: none;
    }

    .login-card .links a:hover {
      text-decoration: underline;
    }

    .password-toggle {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #888;
    }

    .navbar-brand {
      font-size: 1.5rem;
      color: #e91e63;
    }
  </style>
</head>
<body>

<div class="login-container">
  <div class="login-card text-center">
    <!-- Add the brand link here -->
    <div class="container px-0 mb-3">
      <a href="index.php" class="navbar-brand">
        <h1 class="text-primary display-6">Kiddie<span class="text-secondary">Learn</span></h1>
      </a>
    </div>

    <h3>Login</h3>
    <form action="login_process.php" method="POST">
      <!-- Role Selector -->
      <div class="form-floating mb-3">
        <select name="role" class="form-select" id="floatingRole" required>
          <option value="" disabled selected>Select Role</option>
          <option value="teacher">Teacher</option>
          <option value="parent">Parent</option>
        </select>
        <label for="floatingRole"><i class="fas fa-user-tag"></i> Role</label>
      </div>

      <div class="form-floating mb-3">
        <input type="text" name="username" class="form-control" id="floatingUsername" placeholder="Username" required>
        <label for="floatingUsername"><i class="fas fa-user"></i> Username</label>
      </div>

      <!-- Password with toggle -->
      <div class="form-floating mb-3 position-relative">
        <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password" required>
        <label for="floatingPassword"><i class="fas fa-lock"></i> Password</label>
        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
      </div>

      <button type="submit" class="btn btn-primary w-100 py-2">Login</button>
    </form>

    <div class="links mt-3">
      Don't have an account? <a href="register.php">Register Here</a>
    </div>
  </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="loginSuccessModal" tabindex="-1" aria-labelledby="loginSuccessModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4">
      <div class="modal-body">
        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
        <h4 class="mb-2">Login Successful!</h4>
        <p class="mb-0">Welcome back!<br>Redirecting to dashboard...</p>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Show/Hide password
  const togglePassword = document.getElementById('togglePassword');
  const password = document.getElementById('floatingPassword');
  togglePassword.addEventListener('click', function () {
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    this.classList.toggle('fa-eye-slash');
  });

  // Show success modal if ?login=success is in URL
  if (window.location.search.includes('login=success')) {
    var successModal = new bootstrap.Modal(document.getElementById('loginSuccessModal'));
    successModal.show();
    setTimeout(() => { 
      window.location.href = 'dashboard.php'; 
    }, 2000);
  }
</script>

</body>
</html>
