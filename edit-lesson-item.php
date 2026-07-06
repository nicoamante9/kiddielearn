<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

$teacher_id = (int)$_SESSION['user']['id'];
$item_id = (int)($_GET['id'] ?? 0);

// Fetch lesson item with permission check
$stmt = $conn->prepare("
    SELECT li.*, l.lesson_name 
    FROM lesson_items li
    JOIN lessons l ON li.lesson_id = l.id
    WHERE li.id = ? AND l.teacher_id = ?
");
$stmt->bind_param("ii", $item_id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();
$stmt->close();

if (!$item) die("Item not found or permission denied.");

$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['item_title'] ?? '');
    if ($title === '') die("Title is required.");

    $image_path = $item['image_path'];
    $audio_path = $item['audio_path'];

    $uploadDir = "uploads_lessons/lesson_items/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    // Replace image if uploaded
    if (!empty($_FILES['item_image']['name'])) {
        if (file_exists($image_path)) unlink($image_path);
        $fileName = time() . "_" . basename($_FILES['item_image']['name']);
        $targetFile = $uploadDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];
        if (!in_array($fileType, $allowed)) die("Invalid image type.");
        if (!move_uploaded_file($_FILES['item_image']['tmp_name'], $targetFile)) die("Failed to upload image.");
        $image_path = $targetFile;
    }

    // Replace audio if uploaded
    if (!empty($_FILES['item_audio']['name'])) {
        if (!empty($audio_path) && file_exists($audio_path)) unlink($audio_path);
        $audioFileName = time() . "_" . basename($_FILES['item_audio']['name']);
        $audioTarget = $uploadDir . $audioFileName;
        $audioType = strtolower(pathinfo($audioTarget, PATHINFO_EXTENSION));
        $allowedAudio = ['mp3','wav','ogg','m4a'];
        if (!in_array($audioType, $allowedAudio)) die("Invalid audio type.");
        if (!move_uploaded_file($_FILES['item_audio']['tmp_name'], $audioTarget)) die("Failed to upload audio.");
        $audio_path = $audioTarget;
    }

    // Update DB
    $stmt = $conn->prepare("UPDATE lesson_items SET title = ?, image_path = ?, audio_path = ? WHERE id = ?");
    $stmt->bind_param("sssi", $title, $image_path, $audio_path, $item_id);
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
    <title>Edit Item - <?= htmlspecialchars($item['lesson_name']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body style="background-color: #fef8f9;">

<div class="container mt-5">
    <a href="manage-lesson-items.php?lesson_id=<?= $item['lesson_id'] ?>" class="btn btn-primary mb-4">← Back to Lesson Items</a>
    <div class="card p-4 shadow-sm">
        <h3 class="mb-4 text-primary">Edit Item - <?= htmlspecialchars($item['title']) ?></h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Item Title</label>
                <input type="text" name="item_title" class="form-control" value="<?= htmlspecialchars($item['title']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Replace Image</label>
                <input type="file" name="item_image" class="form-control" accept="image/*">
                <?php if (!empty($item['image_path'])): ?>
                    <img src="<?= htmlspecialchars($item['image_path']) ?>" alt="Current Image" class="img-fluid mt-2" style="max-height:200px;">
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Replace Audio (Optional)</label>
                <input type="file" name="item_audio" class="form-control" accept="audio/*">
                <?php if (!empty($item['audio_path'])): ?>
                    <audio controls class="mt-2" style="width:100%;">
                        <source src="<?= htmlspecialchars($item['audio_path']) ?>" type="audio/mpeg">
                    </audio>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-success">Save Changes</button>
        </form>
    </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if ($success): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Item Updated!',
    text: 'The lesson item has been successfully updated.',
    confirmButtonColor: '#e91e63'
}).then(() => {
    window.location.href = "manage-lesson-items.php?lesson_id=<?= $item['lesson_id'] ?>";
});
</script>
<?php endif; ?>
</body>
</html>
