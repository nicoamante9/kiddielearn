<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'parent') {
    header("Location: login.php");
    exit;
}

// Fetch all lessons
$stmt = $conn->prepare("SELECT l.*, u.first_name, u.last_name 
                        FROM lessons l 
                        JOIN users u ON l.teacher_id = u.id 
                        ORDER BY l.created_at DESC");
$stmt->execute();
$lessons = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Lessons - KiddiLearn</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="lib/animate/animate.min.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
body { background-color: #fef8f9; }

.dashboard-card {
    border: 1px solid black;
    border-radius: 20px;
    padding: 60px 20px;
    text-align: center;
    box-shadow: 0 15px 1px rgba(0,0,0,0.05);
    transition: 0.3s;
    background-color: rgb(250,248,249);
    color: #333;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 150px;
}
.dashboard-card:hover {
    background-color: #e91e63;
    color: white;
    transform: translateY(-5px);
}
.dashboard-card i { font-size: 32px; margin-bottom: 15px; display: block; transition: color 0.3s; }
.dashboard-card:hover i { color: white !important; }
</style>
</head>
<body>

<div class="container mt-4">
    <a href="dashboard-parent.php" class="btn btn-primary mb-4">← Back to Dashboard</a>
</div>

<div class="container">
    <div class="row g-4">
        <?php foreach($lessons as $lesson): ?>
        <div class="col-md-6 col-lg-4">
            <a href="parent-lesson-items.php?lesson_id=<?= $lesson['id'] ?>" class="dashboard-card wow fadeInUp text-decoration-none">
                <i class="fas fa-book text-warning"></i>
                <h5><?= htmlspecialchars($lesson['lesson_name']) ?></h5>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="lib/wow/wow.min.js"></script>
<script>new WOW().init();</script>
</body>
</html>
