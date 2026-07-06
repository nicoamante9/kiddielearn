<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

$teacher_id = (int)$_SESSION['user']['id'];
$conn = new mysqli("localhost", "root", "", "kiddielearn");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all activities sent to this teacher including status
$stmt = $conn->prepare("
    SELECT a.id, a.title, a.file_path, a.uploaded_at, a.status,
           u.first_name AS parent_first, u.last_name AS parent_last
    FROM activities a
    JOIN users u ON a.parent_id = u.id
    WHERE a.teacher_id = ?
    ORDER BY a.uploaded_at DESC
");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$activities = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Activities | KiddieLearn</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background-color: #fef8f9; }
        .card-activity {
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            padding: 20px;
            margin-bottom: 20px;
            background: #fff;
            transition: 0.3s;
        }
        .card-activity:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        .card-header { font-weight: bold; }
        .modal-lg-custom { max-width: 90% !important; }
        .preview-img { max-width: 100%; max-height: 80vh; display: block; margin: auto; }
    </style>
</head>
<body class="bg-light">

<div class="container mt-4">
    <a href="dashboard-teacher.php" class="btn btn-primary px-4 py-2 btn-border-radius mb-4">
        ← Back to Dashboard
    </a>
</div>

<div class="container py-5">
    <h2 class="text-center mb-4">📂 Student Activities Submitted</h2>

    <?php if (count($activities) > 0): ?>
        <div class="card card-activity">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered bg-white mb-0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Uploaded By</th>
                                <th>Uploaded At</th>
                                <th>View</th>
                                <th>Download</th>
                                <th>Grade</th>
                                <th>Status</th> <!-- New status column -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($activities as $act): ?>
                                <tr>
                                    <td><?= htmlspecialchars($act['title']) ?></td>
                                    <td><?= htmlspecialchars($act['parent_first'] . ' ' . $act['parent_last']) ?></td>
                                    <td><?= htmlspecialchars($act['uploaded_at']) ?></td>
                                    <td>
                                        <?php if (file_exists($act['file_path'])): ?>
                                            <button class="btn btn-info btn-sm" 
                                                    onclick="openPreview('<?= htmlspecialchars($act['file_path']) ?>')">
                                                View
                                            </button>
                                        <?php else: ?>
                                            <span class="text-danger">File not found</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (file_exists($act['file_path'])): ?>
                                            <a href="<?= htmlspecialchars($act['file_path']) ?>" 
                                               class="btn btn-primary btn-sm" download>
                                                Download
                                            </a>
                                        <?php else: ?>
                                            <span class="text-danger">File not found</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($act['status'] === 'Graded'): ?>
                                            <span class="badge bg-success">Graded</span>
                                        <?php else: ?>
                                            <button class="btn btn-success btn-sm" 
                                                    onclick="openGradeModal(<?= $act['id'] ?>)">
                                                Grade
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($act['status']) ?> <!-- Display current status -->
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No student activities have been uploaded yet.</div>
    <?php endif; ?>
</div>

<!-- Modal for viewing files -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-lg-custom">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">File Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="previewImg" class="preview-img" src="" alt="File Preview">
            </div>
        </div>
    </div>
</div>

<!-- Modal for grading activity -->
<div class="modal fade" id="gradeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="gradeForm" method="POST" action="grade-activity.php">
                <div class="modal-header">
                    <h5 class="modal-title">Grade Activity</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="activity_id" id="activity_id">

                    <!-- Grade dropdown -->
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
                        <label class="form-label">Comment/Feedback</label>
                        <textarea class="form-control" name="comment" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Submit Grade</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openPreview(filePath) {
    const preview = document.getElementById('previewImg');
    preview.src = filePath;
    const viewModal = new bootstrap.Modal(document.getElementById('viewModal'));
    viewModal.show();
}

function openGradeModal(activityId) {
    document.getElementById('activity_id').value = activityId;
    const gradeModal = new bootstrap.Modal(document.getElementById('gradeModal'));
    gradeModal.show();
}

// SweetAlert confirmation for grade submission
document.getElementById('gradeForm').addEventListener('submit', function(e){
    e.preventDefault();
    Swal.fire({
        title: 'Submit Grade?',
        text: "Are you sure you want to submit this grade?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, submit it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            this.submit();
        }
    });
});
</script>

<?php
if(isset($_SESSION['grade_success']) && $_SESSION['grade_success']):
?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Grade Submitted!',
    text: 'The activity has been graded successfully.'
});
</script>
<?php
unset($_SESSION['grade_success']);
unset($_SESSION['graded_activity_id']);
endif;
?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
