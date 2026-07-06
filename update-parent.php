<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'parent') {
    header("Location: login.php");
    exit;
}

$parent = $_SESSION['user'];
$conn = new mysqli("localhost", "root", "", "kiddielearn");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success = false;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $email = trim($_POST["email"]);

    $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, email=? WHERE id=?");
    $stmt->bind_param("sssi", $first_name, $last_name, $email, $parent['id']);
    if ($stmt->execute()) {
    $_SESSION['user']['first_name'] = $first_name;
    $_SESSION['user']['last_name'] = $last_name;
    $_SESSION['user']['email'] = $email;
    $_SESSION['update_success'] = true;

    header("Location: update-parent.php"); // redirect after success
    exit;
}
    $stmt->close();
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $parent['id']);
$stmt->execute();
$parent_data = $stmt->get_result()->fetch_assoc();
$stmt->close();

$stmt = $conn->prepare("SELECT * FROM children WHERE parent_id = ?");
$stmt->bind_param("i", $parent['id']);
$stmt->execute();
$children = $stmt->get_result();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Parent’s Update - KiddiLearn</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { background-color: #fef8f9; }
        .card-header {
            background-color: #e91e63;
            color: white;
            font-weight: bold;
            padding: 15px;
            border-radius: 10px 10px 0 0;
        }
        .card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }
        .btn-primary {
            background-color: #e91e63;
            border: none;
        }
        .btn-primary:hover {
            background-color: #d81b60;
        }
        .btn-success {
            background-color: #4caf50;
        }
        .modal-content {
            background-color: #fff !important;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <a href="dashboard-parent.php" class="btn btn-primary px-4 py-2 btn-border-radius">
        ← Back to Dashboard
    </a>
</div>
<div class="container py-5">
    <h2 class="text-center mb-4 text-primary">
        <i class="fas fa-user-cog me-2"></i>Parent’s Update
    </h2>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Profile Card -->
            <div class="card mb-5">
                <div class="card-header">
                    <i class="fas fa-user-edit me-2"></i>Update Account Information
                </div>
                <div class="card-body p-4">
                    <form method="POST" id="updateProfileForm">
                        <input type="hidden" name="update_profile" value="1">
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" name="first_name" id="first_name" value="<?= htmlspecialchars($parent_data['first_name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="last_name" id="last_name" value="<?= htmlspecialchars($parent_data['last_name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email" id="email" value="<?= htmlspecialchars($parent_data['email']) ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i>Save Changes
                        </button>
                    </form>
                </div>
            </div>

            <!-- Children Management -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-child me-2"></i>Manage Children
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Your Child/Children</h5>
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addChildModal">
                            <i class="fas fa-plus me-1"></i>Add Child
                        </button>
                    </div>

                    <?php if ($children->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Age</th>
                                        <th style="width: 120px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($child = $children->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($child['first_name']) ?></td>
                                            <td><?= htmlspecialchars($child['last_name']) ?></td>
                                            <td><?= $child['age'] ?></td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editChildModal<?= $child['id'] ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $child['id'] ?>)">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                            <?php foreach ($children as $child): ?>
<!-- Edit Child Modal Outside Table -->
<div class="modal fade" id="editChildModal<?= $child['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="edit-child.php" class="modal-content">
            <input type="hidden" name="child_id" value="<?= $child['id'] ?>">
            <div class="modal-header">
                <h5 class="modal-title">Edit Child</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($child['first_name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($child['last_name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Age</label>
                    <input type="number" name="age" class="form-control" value="<?= $child['age'] ?>" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
<?php endforeach; ?>

                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">You haven’t added any children yet.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Child Modal -->
<div class="modal fade" id="addChildModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="add-child.php" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Child</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>First Name</label>
                    <input type="text" class="form-control" name="first_name" required>
                </div>
                <div class="mb-3">
                    <label>Last Name</label>
                    <input type="text" class="form-control" name="last_name" required>
                </div>
                <div class="mb-3">
                    <label>Age</label>
                    <input type="number" class="form-control" name="age" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Add Child</button>
            </div>
        </form>
    </div>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('updateProfileForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Stop the form from submitting right away

    Swal.fire({
        title: 'Confirm Update',
        text: 'Are you sure you want to save these changes?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#e91e63',
        cancelButtonColor: '#aaa',
        confirmButtonText: 'Yes, save it!'
    }).then((result) => {
        if (result.isConfirmed) {
            e.target.submit(); // Submit the form if confirmed
        }
    });
});
    function confirmDelete(childId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to delete this child.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e91e63',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "delete-child.php?child_id=" + childId;
            }
        });
    }

    <?php if (isset($_SESSION['update_success'])): ?>
Swal.fire({
    icon: 'success',
    title: 'Profile Updated',
    text: 'Your information was successfully updated!',
    confirmButtonColor: '#e91e63'
});
<?php unset($_SESSION['update_success']); endif; ?>

    <?php if (isset($_SESSION['child_success'])): ?>
Swal.fire({
    icon: 'success',
    title: 'Success',
    text: '<?= $_SESSION['child_success'] ?>',
    confirmButtonColor: '#e91e63'
});
<?php unset($_SESSION['child_success']); endif; ?>

    document.querySelector('#addChildModal form').addEventListener('submit', function(e) {
    e.preventDefault(); // prevent normal form submission

    Swal.fire({
        title: 'Add Child?',
        text: 'Do you want to add this child to your account?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4caf50',
        cancelButtonColor: '#aaa',
        confirmButtonText: 'Yes, add child!'
    }).then((result) => {
        if (result.isConfirmed) {
            e.target.submit(); // proceed with submission if confirmed
        }
    });
});
document.querySelectorAll('form[action="edit-child.php"]').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Stop normal submission

        Swal.fire({
            title: 'Save Changes?',
            text: 'Do you want to save the changes for this child?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#e91e63',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Yes, save it!'
        }).then((result) => {
            if (result.isConfirmed) {
                e.target.submit(); // Submit if confirmed
            }
        });
    });
});
</script>

</body>
</html>
