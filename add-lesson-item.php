<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

$teacher_id = (int)$_SESSION['user']['id'];
$lesson_id = (int)($_GET['lesson_id'] ?? 0);

// Fetch lesson
$stmt = $conn->prepare("SELECT * FROM lessons WHERE id = ? AND teacher_id = ?");
$stmt->bind_param("ii", $lesson_id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$lesson = $result->fetch_assoc();
$stmt->close();

if (!$lesson) {
    die("Lesson not found or permission denied.");
}

$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_title = trim($_POST['item_title'] ?? '');
    if ($item_title === '') die("Item title is required.");

    // Handle image upload
    if (empty($_FILES['item_image']['name'])) die("Image is required.");
    $uploadDir = "uploads_lessons/lesson_items/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $fileName = time() . "_" . basename($_FILES['item_image']['name']);
    $targetFile = $uploadDir . $fileName;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif'];

    if (!in_array($fileType, $allowed)) die("Invalid image type.");
    if (!move_uploaded_file($_FILES['item_image']['tmp_name'], $targetFile)) die("Failed to upload image.");
    $image_path = $targetFile;

    $audio_path = null;
    if (!empty($_FILES['item_audio']['name'])) {
        $audioFileName = time() . "_" . basename($_FILES['item_audio']['name']);
        $audioTarget = $uploadDir . $audioFileName;
        $audioType = strtolower(pathinfo($audioTarget, PATHINFO_EXTENSION));
        $allowedAudio = ['mp3','wav','ogg','m4a'];
        if (!in_array($audioType, $allowedAudio)) die("Invalid audio type.");
        if (!move_uploaded_file($_FILES['item_audio']['tmp_name'], $audioTarget)) die("Failed to upload audio.");
        $audio_path = $audioTarget;
    }

    // Insert into DB
    $stmt = $conn->prepare("INSERT INTO lesson_items (lesson_id, title, image_path, audio_path, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("isss", $lesson_id, $item_title, $image_path, $audio_path);
    if ($stmt->execute()) {
        $stmt->close();
        $success = true;
    } else {
        die("Database error: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Item - <?= htmlspecialchars($lesson['lesson_name']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body style="background-color: #fef8f9;">

<div class="container mt-5">
    <a href="manage-lesson-items.php?lesson_id=<?= $lesson_id ?>" class="btn btn-primary mb-4">← Back to Lesson Items</a>
    <div class="card p-4 shadow-sm">
        <h3 class="mb-4 text-primary">Add New Item to <?= htmlspecialchars($lesson['lesson_name']) ?></h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Item Title</label>
                <input type="text" name="item_title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Upload Image</label>
                <input type="file" name="item_image" class="form-control" accept="image/*" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Upload Audio (Optional)</label>
                <input type="file" name="item_audio" class="form-control" accept="audio/*">
            </div>
            <button type="submit" class="btn btn-success">Add Item</button>
        </form>
    </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if ($success): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Item Added!',
    text: 'The lesson item has been successfully added.',
    confirmButtonColor: '#e91e63'
}).then(() => {
    window.location.href = "manage-lesson-items.php?lesson_id=<?= $lesson_id ?>";
});
</script>
<?php endif; ?>
</body>
</html>
