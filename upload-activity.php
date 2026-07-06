<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'parent') {
    header("Location: login.php");
    exit;
}

// DB connection
$conn = new mysqli("localhost", "root", "", "kiddielearn");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get all teachers to populate the select box
$teachers = [];
$result = $conn->query("SELECT id, first_name, last_name FROM users WHERE role='teacher'");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $teachers[] = $row;
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $teacher_id = (int)$_POST['teacher_id'];
    $title = trim($_POST['title']);
    $parent_id = (int)$_SESSION['user']['id'];
    $upload_success = false;

    // If painted image is preloaded
    if (isset($_POST['preload_file']) && !empty($_POST['preload_file'])) {
        $file_path = $_POST['preload_file'];
        $upload_success = true;
    } else {
        $file = $_FILES['activity_file'];

        if (empty($teacher_id) || empty($title) || empty($file['name'])) {
            $_SESSION['upload_error'] = 'All fields are required.';
            header("Location: upload-activity.php");
            exit;
        }

        // File upload
        $target_dir = "uploads/activities/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $filename = time() . "_" . basename($file["name"]);
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            $file_path = $target_file;
            $upload_success = true;
        } else {
            $_SESSION['upload_error'] = 'Failed to upload file.';
            header("Location: upload-activity.php");
            exit;
        }
    }

    // Insert record with relative path
    if ($upload_success) {
        $stmt = $conn->prepare("INSERT INTO activities (parent_id, teacher_id, title, file_path, uploaded_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiss", $parent_id, $teacher_id, $title, $file_path);
        if ($stmt->execute()) {
            $_SESSION['upload_success'] = true;
        } else {
            $_SESSION['upload_error'] = 'Database error.';
        }
    }

    header("Location: upload-activity.php");
    exit;
}

$conn->close();

// Check for preloaded painted file
$preload_file = isset($_GET['painted']) ? $_GET['painted'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Upload Activity - Parent | KiddieLearn</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">
<div class="container mt-4">
    <a href="dashboard-parent.php" class="btn btn-primary px-4 py-2 btn-border-radius">
        ← Back to Dashboard
    </a>
</div>

<div class="container py-5">
    <h2 class="mb-4">📄 Upload Activity</h2>
    <form method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm" id="uploadForm">
        <div class="mb-3">
            <label class="form-label">Select Teacher</label>
            <select name="teacher_id" class="form-select" required>
                <option value="">-- Select a Teacher --</option>
                <?php foreach ($teachers as $teacher): ?>
                    <option value="<?= $teacher['id'] ?>">
                        <?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Activity Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Select File</label>
            <input type="file" name="activity_file" class="form-control" accept=".pdf,.doc,.docx,.ppt,.pptx,.png,.jpg,.jpeg,.gif" <?= $preload_file ? '' : 'required' ?>>
        </div>

        <?php if($preload_file): ?>
            <div class="mb-3">
                <label class="form-label">Preloaded Painted File</label>
                <div>
                    <img src="<?= htmlspecialchars($preload_file) ?>" alt="Preloaded file" style="max-width:200px;">
                </div>
                <input type="hidden" name="preload_file" value="<?= htmlspecialchars($preload_file) ?>">
            </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary">Upload Activity</button>
    </form>
</div>

<script>
document.getElementById('uploadForm').addEventListener('submit', function(e){
    e.preventDefault();

    Swal.fire({
        title: 'Are you sure?',
        text: "Do you want to upload this activity?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, upload it!',
    }).then((result)=>{
        if(result.isConfirmed){
            this.submit();
        }
    });
});

// SweetAlert success or error after redirect
<?php if(isset($_SESSION['upload_success']) && $_SESSION['upload_success']): ?>
    Swal.fire({
        icon: 'success',
        title: 'Uploaded!',
        text: 'Your activity has been successfully uploaded.',
    });
    <?php unset($_SESSION['upload_success']); ?>
<?php elseif(isset($_SESSION['upload_error'])): ?>
    Swal.fire({
        icon: 'error',
        title: 'Oops!',
        text: '<?= $_SESSION['upload_error'] ?>',
    });
    <?php unset($_SESSION['upload_error']); ?>
<?php endif; ?>
</script>
</body>
</html>
