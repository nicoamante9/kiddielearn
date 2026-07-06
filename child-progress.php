<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'parent') {
    header("Location: login.php");
    exit;
}

$parent_id = $_SESSION['user']['id'];
$conn = new mysqli("localhost", "root", "", "kiddielearn");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// Get all children of this parent
$children_stmt = $conn->prepare("SELECT * FROM children WHERE parent_id = ?");
$children_stmt->bind_param("i", $parent_id);
$children_stmt->execute();
$children_result = $children_stmt->get_result();

$progress_by_child = [];
$activity_grades_by_child = [];

while ($child = $children_result->fetch_assoc()) {
    $child_id = $child['id'];

    // Regular progress
    $progress_stmt = $conn->prepare("
        SELECT p.topic, p.grade, p.graded_at, p.comment,
               u.first_name AS grader_first, u.last_name AS grader_last
        FROM progress p
        LEFT JOIN users u ON p.graded_by = u.id
        WHERE p.child_id = ?
        ORDER BY p.graded_at DESC
    ");
    $progress_stmt->bind_param("i", $child_id);
    $progress_stmt->execute();
    $progress_result = $progress_stmt->get_result();

    $progress = [];
    while ($row = $progress_result->fetch_assoc()) {
        $graded_by_name = ($row['grader_first'] && $row['grader_last']) 
            ? $row['grader_first'] . ' ' . $row['grader_last'] 
            : 'Not graded';
        $row['graded_by_name'] = $graded_by_name;
        $progress[] = $row;
    }
    $progress_by_child[$child['first_name']] = $progress;
    $progress_stmt->close();

    // Activity Grades (from graded_activities)
    $activity_stmt = $conn->prepare("
        SELECT a.title AS topic, ga.grade, ga.graded_at, ga.comment,
               u.first_name AS grader_first, u.last_name AS grader_last
        FROM graded_activities ga
        JOIN activities a ON ga.activity_id = a.id
        LEFT JOIN users u ON ga.graded_by = u.id
        WHERE a.parent_id = ?
        ORDER BY ga.graded_at DESC
    ");
    $activity_stmt->bind_param("i", $parent_id);
    $activity_stmt->execute();
    $activity_result = $activity_stmt->get_result();

    $activity_grades = [];
    while ($row = $activity_result->fetch_assoc()) {
        $graded_by_name = ($row['grader_first'] && $row['grader_last']) 
            ? $row['grader_first'] . ' ' . $row['grader_last'] 
            : 'Not graded';
        $row['graded_by_name'] = $graded_by_name;
        $activity_grades[] = $row;
    }
    $activity_grades_by_child[$child['first_name']] = $activity_grades;
    $activity_stmt->close();
}

$children_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Progress | KiddieLearn</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">
<div class="container mt-4">
    <a href="dashboard-parent.php" class="btn btn-primary px-4 py-2 btn-border-radius">← Back to Dashboard</a>
</div>

<div class="container py-5">
    <h2 class="mb-4">📈 Progress for All Children</h2>

    <?php if (count($progress_by_child) > 0): ?>
        <?php foreach ($progress_by_child as $child_name => $progress_list): ?>
            <div class="card mb-4">
                <div class="card-header bg-primary text-white"><?= htmlspecialchars($child_name) ?>'s Progress</div>
                <div class="card-body">
                    <!-- Regular Progress -->
                    <h5>Lesson/Topic Progress</h5>
                    <?php if (count($progress_list) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered bg-white mb-3">
                                <thead>
                                    <tr>
                                        <th>Topic</th>
                                        <th>Grade</th>
                                        <th>Graded At</th>
                                        <th>Graded By</th>
                                        <th>Comment/Feedback</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($progress_list as $progress): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($progress['topic']) ?></td>
                                            <td><?= htmlspecialchars($progress['grade']) ?></td>
                                            <td><?= htmlspecialchars($progress['graded_at']) ?></td>
                                            <td><?= htmlspecialchars($progress['graded_by_name']) ?></td>
                                            <td><?= htmlspecialchars($progress['comment'] ?? '—') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning mb-3">No lesson progress records for this child yet.</div>
                    <?php endif; ?>

                    <!-- Activity Grades -->
                    <h5>📄 Activity Grades</h5>
                    <?php if (!empty($activity_grades_by_child[$child_name])): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered bg-white mb-0">
                                <thead>
                                    <tr>
                                        <th>Activity</th>
                                        <th>Grade</th>
                                        <th>Graded At</th>
                                        <th>Graded By</th>
                                        <th>Comment/Feedback</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($activity_grades_by_child[$child_name] as $act): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($act['topic']) ?></td>
                                            <td><?= htmlspecialchars($act['grade']) ?></td>
                                            <td><?= htmlspecialchars($act['graded_at']) ?></td>
                                            <td><?= htmlspecialchars($act['graded_by_name']) ?></td>
                                            <td><?= htmlspecialchars($act['comment'] ?? '—') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">No activity grades for this child yet.</div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info">You haven’t added any children yet.</div>
    <?php endif; ?>
</div>
</body>
</html>
