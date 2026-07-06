<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

$teacher = $_SESSION['user'];
$teacher_id = (int)$teacher['id'];

// Fetch lessons from database
$stmt = $conn->prepare("SELECT * FROM lessons WHERE teacher_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$lessons = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Check for success messages from query params
$success = $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Lessons - KiddiLearn</title>
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<link href="img/favicon.ico" rel="icon">
<link href="lib/animate/animate.min.css" rel="stylesheet">
<link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
body { background-color: rgb(254, 248, 252); }

.dashboard-card {
    border: 1px solid black;
    border-radius: 20px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 15px 1px rgba(0,0,0,0.05);
    transition: 0.3s;
    background-color: rgb(250, 248, 249);
    color: #333;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 150px;
    margin-bottom: 20px;
    position: relative; /* Make the card the reference point for the dropdown */
}
.dashboard-card:hover {
    background-color: #e91e63;
    color: white;
    transform: translateY(-5px);
}
.dashboard-card i { font-size: 32px; margin-bottom: 15px; display: block; }
.dashboard-card:hover i { color: white !important; }

.lesson-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr); /* 3 cards per row */
    gap: 20px;
    margin-top: 30px;
}

.add-lesson-container {
    margin-bottom: 30px;
    display: flex;
    justify-content: flex-end; /* Align the Add button to the right */
    gap: 10px;
    flex-wrap: wrap;
}

/* Dropdown position */
.dropdown {
    position: absolute;
    top: 10px;
    left: 10px;
    z-index: 10;
}

</style>
</head>
<body>

<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
      <a href="dashboard-teacher.php" class="btn btn-primary px-4 py-2 btn-border-radius mb-2">
        ← Back to Dashboard
      </a>

      <!-- Add Lesson Button -->
      <div class="add-lesson-container">
          <button class="btn btn-success btn-add-lesson px-4 py-2" data-bs-toggle="modal" data-bs-target="#addLessonModal">
              <i class="fa fa-plus"></i> Add Lesson
          </button>
      </div>
  </div>

  <div class="container text-center mb-5">
    <h1 class="display-4 wow fadeInDown" data-wow-delay="0.1s">
      <span class="text-primary" style="font-family: 'Pacifico', cursive;">Manage</span>
      <span class="text-secondary" style="font-family: 'Pacifico', cursive;">Lessons</span>
    </h1>
  </div>

  <!-- Lessons Grid -->
  <div class="lesson-cards">
      <!-- Loop through lessons -->
      <?php foreach($lessons as $lesson): ?>
      <div class="dashboard-card wow fadeInUp text-decoration-none">
          <a href="manage-lesson-items.php?lesson_id=<?= $lesson['id'] ?>" style="text-decoration:none; color: inherit;">
              <i class="fas fa-book text-warning"></i>
              <h5><?= htmlspecialchars($lesson['lesson_name']) ?></h5>
          </a>

          <!-- Dropdown for Edit/Delete -->
          <div class="dropdown">
              <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  <span class="visually-hidden">Actions</span>
              </button>
              <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editLessonModal" onclick="populateEditModal(<?= $lesson['id'] ?>, '<?= htmlspecialchars($lesson['lesson_name'], ENT_QUOTES) ?>')">Edit</a></li>
                  <li><a class="dropdown-item text-danger" href="#" onclick="deleteLesson(<?= $lesson['id'] ?>)">Delete</a></li>
              </ul>
          </div>
      </div>
      <?php endforeach; ?>
  </div>

<!-- Add Lesson Modal -->
<div class="modal fade" id="addLessonModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="add-lesson.php">
      <div class="modal-header">
        <h5 class="modal-title">Add Lesson</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="teacher_id" value="<?= $teacher_id ?>">
        <div class="mb-3">
          <label class="form-label">Lesson Name</label>
          <input type="text" class="form-control" name="lesson_name" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Add Lesson</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Lesson Modal -->
<div class="modal fade" id="editLessonModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="edit-lesson.php">
      <div class="modal-header">
        <h5 class="modal-title">Edit Lesson</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="lesson_id" id="editLessonId">
        <div class="mb-3">
          <label class="form-label">Lesson Name</label>
          <input type="text" class="form-control" name="lesson_name" id="editLessonName" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="lib/wow/wow.min.js"></script>
<script>
new WOW().init();

function populateEditModal(id, name) {
    document.getElementById('editLessonId').value = id;
    document.getElementById('editLessonName').value = name;
}

function deleteLesson(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will delete the lesson permanently!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'delete-lesson.php?id=' + id;
        }
    });
}

// SweetAlert success messages
<?php if($success === 'deleted'): ?>
Swal.fire({ icon: 'success', title: 'Deleted!', text: 'Lesson has been deleted successfully.' });
<?php elseif($success === 'added'): ?>
Swal.fire({ icon: 'success', title: 'Added!', text: 'Lesson has been added successfully.' });
<?php elseif($success === 'edited'): ?>
Swal.fire({ icon: 'success', title: 'Saved!', text: 'Lesson changes have been saved successfully.' });
<?php endif; ?>
</script>

</body>
</html>
