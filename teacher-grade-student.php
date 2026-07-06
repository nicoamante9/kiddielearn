<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

// DB connection
$conn = new mysqli("localhost", "root", "", "kiddielearn");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all children and their parents
$children = [];
$sql = "SELECT children.id, children.first_name, children.last_name, users.first_name AS parent_first_name 
        FROM children 
        JOIN users ON children.parent_id = users.id";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $children[] = $row;
    }
}

// Fetch lessons for the topic dropdown
$lessons = [];
$teacher_id = (int)$_SESSION['user']['id'];
$lessonQuery = $conn->prepare("SELECT id, lesson_name FROM lessons WHERE teacher_id = ?");
$lessonQuery->bind_param("i", $teacher_id);
$lessonQuery->execute();
$lessonResult = $lessonQuery->get_result();
if ($lessonResult && $lessonResult->num_rows > 0) {
    while($lesson = $lessonResult->fetch_assoc()) {
        $lessons[] = $lesson;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grade Student - KiddieLearn</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background: #fef8f9; }
        .card { border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 30px; background: #fff; }
    </style>
</head>
<body>
<div class="container mt-4">
  <a href="dashboard-teacher.php" class="btn btn-primary px-4 py-2 btn-border-radius">← Back to Dashboard</a>
</div>

<div class="container py-5">
    <div class="text-center mb-4">
        <h2 class="text-primary">📋 Grade Student</h2>
    </div>

    <div class="card mx-auto" style="max-width: 500px;">
        <form id="gradeForm" action="grade-student-process.php" method="POST">
            <!-- Student Search + Dropdown -->
            <div class="mb-3">
                <label for="studentSearch" class="form-label">Search Student</label>
                <input type="text" id="studentSearch" class="form-control" placeholder="Type to search... and select student on the dropdown">
            </div>

            <div class="mb-3">
                <label for="child_id" class="form-label">Select Student</label>
                <select name="child_id" id="childDropdown" class="form-select" required>
                    <option value="">-- Select Student --</option>
                    <?php foreach($children as $child): ?>
                        <option value="<?= $child['id'] ?>">
                            <?= htmlspecialchars($child['first_name'] . " " . $child['last_name']) ?> 
                            (Parent: <?= htmlspecialchars($child['parent_first_name']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="topic" class="form-label">Select Topic</label>
                <select name="topic" id="topic" class="form-select" required>
                    <option value="">-- Select --</option>
                    <?php foreach($lessons as $lesson): ?>
                        <option value="<?= htmlspecialchars($lesson['lesson_name']) ?>">
                            <?= htmlspecialchars($lesson['lesson_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="grade" class="form-label">Grade</label>
                <select name="grade" id="grade" class="form-select" required>
                    <option value="">-- Select Grade --</option>
                    <option value="A+">A+ (Excellent)</option>
                    <option value="A">A (Very Good)</option>
                    <option value="B+">B+ (Good)</option>
                    <option value="C">C (Satisfactory)</option>
                    <option value="D">D (Needs Improvement)</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="comment" class="form-label">Comment (Optional)</label>
                <textarea name="comment" id="comment" class="form-control" rows="3" placeholder="Write feedback or notes here..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-100">Submit Grade</button>
        </form>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
<script>
    Swal.fire({ icon: 'success', title: 'Success!', text: 'Grade saved successfully!', confirmButtonColor: '#e91e63' });
</script>
<?php endif; ?>

<script src="js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('studentSearch').addEventListener('keyup', function() {
    var filter = this.value.toLowerCase();
    var options = document.getElementById('childDropdown').options;
    for (var i = 0; i < options.length; i++) {
        options[i].style.display = options[i].text.toLowerCase().includes(filter) ? '' : 'none';
    }
});

document.getElementById('gradeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var studentText = document.getElementById('childDropdown').selectedOptions[0].text;
    var grade = document.getElementById('grade').value;
    Swal.fire({
        title: 'Are you sure?',
        html: `You are about to submit grade <b>${grade}</b> for <b>${studentText}</b>.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e91e63',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, submit it!',
        cancelButtonText: 'Cancel'
    }).then((result) => { if (result.isConfirmed) this.submit(); });
});
</script>
</body>
</html>
