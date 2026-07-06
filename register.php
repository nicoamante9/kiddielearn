<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KiddieLearn Register</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <!-- Bootstrap CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        body {
            background: url('images/bg-login.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', sans-serif;
        }
        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(5px);
        }
        .register-card {
            width: 100%;
            max-width: 420px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .register-card h3 {
            color: #e91e63;
            font-weight: 700;
            margin-bottom: 25px;
        }
        .form-floating label i {
            margin-right: 8px;
            color: #999;
        }
        .register-card .btn {
            background-color: #e91e63;
            border: none;
        }
        .register-card .btn:hover {
            background-color: #d81b60;
        }
        .register-card .links {
            margin-top: 20px;
            font-size: 14px;
        }
        .register-card .links a {
            color: #e91e63;
            text-decoration: none;
        }
        .register-card .links a:hover {
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
    </style>
</head>
<body>

<div class="register-container">
    <div class="register-card text-center">
        <h3>Create Account</h3>
        <form id="registerForm" action="register_process.php" method="POST" novalidate>
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
                <input type="text" name="first_name" class="form-control" id="floatingFirstName" placeholder="First Name" required>
                <label for="floatingFirstName"><i class="fas fa-user"></i> First Name</label>
            </div>
            <div class="form-floating mb-3">
                <input type="text" name="last_name" class="form-control" id="floatingLastName" placeholder="Last Name" required>
                <label for="floatingLastName"><i class="fas fa-user"></i> Last Name</label>
            </div>
            <div class="form-floating mb-3">
                <input type="email" name="email" class="form-control" id="floatingEmail" placeholder="Email" required>
                <label for="floatingEmail"><i class="fas fa-envelope"></i> Email</label>
            </div>
            <div class="form-floating mb-3">
                <input type="text" name="username" class="form-control" id="floatingUsername" placeholder="Username" required>
                <label for="floatingUsername"><i class="fas fa-user-circle"></i> Username</label>
            </div>

            <!-- Password -->
            <div class="form-floating mb-3 position-relative">
                <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password" required>
                <label for="floatingPassword"><i class="fas fa-lock"></i> Password</label>
                <i class="fas fa-eye password-toggle" id="togglePassword"></i>
            </div>
            <div class="form-floating mb-3 position-relative">
                <input type="password" name="confirm_password" class="form-control" id="floatingConfirmPassword" placeholder="Confirm Password" required>
                <label for="floatingConfirmPassword"><i class="fas fa-lock"></i> Confirm Password</label>
                <i class="fas fa-eye password-toggle" id="toggleConfirmPassword"></i>
                <div id="password-message" class="password-match text-start"></div>
            </div>

            <!-- Parent-specific fields -->
            <div id="parentFields" style="display:none;">
                <hr>
                <h6 class="text-start mb-3">Child Information</h6>
                <div class="form-floating mb-3">
                    <input type="text" name="child_first_name" class="form-control" id="childFirstName" placeholder="Child First Name">
                    <label for="childFirstName"><i class="fas fa-child"></i> Child First Name</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" name="child_last_name" class="form-control" id="childLastName" placeholder="Child Last Name">
                    <label for="childLastName"><i class="fas fa-child"></i> Child Last Name</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="number" name="child_age" class="form-control" id="childAge" placeholder="Child Age" min="1">
                    <label for="childAge"><i class="fas fa-birthday-cake"></i> Child Age</label>
                </div>
            </div>

            <button type="submit" id="submitBtn" class="btn btn-primary w-100 py-2">Register</button>
        </form>

        <div class="links mt-3">
            Already have an account? <a href="login.php">Login Here</a>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // DOM elements
    const roleSelect = document.getElementById('floatingRole');
    const parentFields = document.getElementById('parentFields');

    const childFirst = document.getElementById('childFirstName');
    const childLast = document.getElementById('childLastName');
    const childAge = document.getElementById('childAge');

    // Password toggles
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('floatingPassword');
    togglePassword.addEventListener('click', function () {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.classList.toggle('fa-eye-slash');
    });

    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const confirmPassword = document.getElementById('floatingConfirmPassword');
    toggleConfirmPassword.addEventListener('click', function () {
        const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmPassword.setAttribute('type', type);
        this.classList.toggle('fa-eye-slash');
    });

    // Password match check visual
    const message = document.getElementById('password-message');
    function checkPasswords() {
        if (confirmPassword.value.length === 0 && password.value.length === 0) {
            message.textContent = '';
            return;
        }
        if (password.value === confirmPassword.value) {
            message.style.color = 'green';
            message.textContent = '✅ Passwords match!';
        } else {
            message.style.color = 'red';
            message.textContent = '❌ Passwords do not match.';
        }
    }
    password.addEventListener('keyup', checkPasswords);
    confirmPassword.addEventListener('keyup', checkPasswords);

    // Role-based field toggle and "required" toggling
    function updateParentFields(role) {
        if (role === 'parent') {
            parentFields.style.display = 'block';
            childFirst.required = true;
            childLast.required = true;
            childAge.required = true;
        } else {
            parentFields.style.display = 'none';
            childFirst.required = false;
            childLast.required = false;
            childAge.required = false;
            // clear validation message if any
        }
    }

    // Initialize based on current value (in case of browser restore)
    updateParentFields(roleSelect.value);

    roleSelect.addEventListener('change', function () {
        updateParentFields(this.value);
    });

    // Form submit handler: enforce password match and basic checks
    const registerForm = document.getElementById('registerForm');
    registerForm.addEventListener('submit', function (e) {
        // Password match
        if (password.value !== confirmPassword.value) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Password Mismatch',
                text: 'Please make sure both passwords match.',
                confirmButtonColor: '#e91e63'
            });
            return;
        }

        // If parent role, ensure child fields are filled (extra safety)
        if (roleSelect.value === 'parent') {
            if (!childFirst.value.trim() || !childLast.value.trim() || !childAge.value) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Child Information',
                    text: 'Please fill out all child fields before registering.',
                    confirmButtonColor: '#e91e63'
                });
                return;
            }
        }

        // allow submit to proceed
    });

    // Show SweetAlert for server-side messages from query string
    const params = new URLSearchParams(window.location.search);
    if (params.has('success') && params.get('success') === '1') {
        Swal.fire({
            title: 'Registration Successful!',
            text: 'Your account has been created. Redirecting to login...',
            icon: 'success',
            confirmButtonColor: '#e91e63',
            showConfirmButton: false,
            timer: 2200
        }).then(() => {
            window.location.href = 'login.php';
        });
    } else if (params.has('error')) {
        const err = params.get('error');
        let title = 'Error';
        let text = 'Something went wrong.';

        switch (err) {
            case 'empty':
                title = 'Missing Information';
                text = 'Please fill in all required fields.';
                break;
            case 'childempty':
                title = 'Missing Child Information';
                text = 'Please complete the child information for parent accounts.';
                break;
            case 'nomatch':
                title = 'Password Mismatch';
                text = 'Passwords do not match. Please try again.';
                break;
            case 'exists':
                title = 'Username Taken';
                text = 'The chosen username already exists. Please pick another username.';
                break;
            case 'childfail':
                title = 'Child Save Failed';
                text = 'Account created but saving child info failed. Please contact admin.';
                break;
            case 'failed':
            default:
                title = 'Registration Failed';
                text = 'Unable to create account. Try again later.';
                break;
        }

        Swal.fire({
            icon: 'error',
            title: title,
            text: text,
            confirmButtonColor: '#e91e63'
        });
    }
</script>

</body>
</html>
