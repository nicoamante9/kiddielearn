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

// Get children
$children = [];
$result = $conn->query("SELECT c.id, c.first_name, c.last_name, 
                        u.first_name AS parent_first_name, u.last_name AS parent_last_name 
                        FROM children c 
                        JOIN users u ON c.parent_id = u.id");
while ($row = $result->fetch_assoc()) {
    $children[] = $row;
}

// Get weeks
$weeks = [];
$week_result = $conn->query("SELECT * FROM weeks");
while ($row = $week_result->fetch_assoc()) {
    $weeks[] = $row;
}

// Handle Create Week POST (normal POST, not AJAX)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_week'])) {
    $new_week = trim($_POST['new_week']);
    $stmt = $conn->prepare("INSERT INTO weeks (week_name) VALUES (?)");
    $stmt->bind_param("s", $new_week);
    $stmt->execute();
    $_SESSION['week_created'] = $new_week;
    header("Location: upload-worksheet.php");
    exit;
}

// AJAX file upload handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_upload'])) {

    $child_id = $_POST['child_id'];
    $title = trim($_POST['title']);
    $week = trim($_POST['week']);
    $feedback = trim($_POST['feedback']);

    if (empty($child_id) || empty($title) || empty($week)) {
        echo json_encode(['status'=>'error','message'=>'Child, title, and week are required.']);
        exit;
    }

    if (!isset($_FILES['files'])) {
        echo json_encode(['status'=>'error','message'=>'No files uploaded.']);
        exit;
    }

    $allowed_extensions = ['pdf','doc','docx','ppt','pptx','jpg','jpeg','png','gif'];
    $uploaded_files = $_FILES['files'];

    for ($i = 0; $i < count($uploaded_files['name']); $i++) {
        $file_name = $uploaded_files['name'][$i];
        $file_tmp = $uploaded_files['tmp_name'][$i];
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (!in_array($file_extension, $allowed_extensions)) continue;

        $target_dir = "uploads/worksheets/";
        $filename = time() . "_" . rand(1000,9999) . "_" . basename($file_name);
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($file_tmp, $target_file)) {
            $stmt = $conn->prepare("INSERT INTO worksheets 
                (child_id, title, week, file_path, feedback, uploaded_at) 
                VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("issss", $child_id, $title, $week, $filename, $feedback);
            $stmt->execute();
        }
    }

    echo json_encode(['status'=>'success','message'=>'All files uploaded successfully.']);
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Worksheet - Teacher | KiddieLearn</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        #preview-box {
            margin-top: 15px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 10px;
        }
        .preview-item {
            border: 1px solid #ddd;
            background: #fff;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            font-size: 13px;
        }
        .preview-item img {
            width: 100%;
            height: 90px;
            object-fit: cover;
            border-radius: 6px;
        }
        .file-icon {
            font-size: 40px;
        }
        .remove-btn {
            cursor: pointer;
            color: red;
            font-weight: bold;
            font-size: 14px;
            margin-top: 5px;
            display: block;
        }
    </style>
</head>
<body class="bg-light">

<div class="container mt-4">
  <a href="dashboard-teacher.php" class="btn btn-primary px-4 py-2 btn-border-radius">← Back to Dashboard</a>
</div>

<div class="container py-5">
    <h2 class="mb-4">📄 Upload Worksheet</h2>

    <form id="upload-form" class="bg-white p-4 rounded shadow-sm">

        <div class="mb-3">
            <label class="form-label">Select Student</label>
            <select name="child_id" id="child_id" class="form-select" required>
                <option value="">-- Select a Student --</option>
                <?php foreach ($children as $child): ?>
                    <option value="<?= $child['id'] ?>">
                        <?= htmlspecialchars($child['first_name']." ".$child['last_name']) ?> 
                        (Parent: <?= htmlspecialchars($child['parent_first_name']." ".$child['parent_last_name']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Worksheet Title</label>
            <input type="text" name="title" id="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Select Week</label>
            <select name="week" id="week" class="form-select" required>
                <option value="">-- Select a Week --</option>
                <?php foreach ($weeks as $w): ?>
                    <option value="<?= htmlspecialchars($w['week_name']) ?>"><?= htmlspecialchars($w['week_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#createWeekModal">Create Week</button>
        </div>

        <div class="mb-3">
            <label class="form-label">Feedback</label>
            <textarea name="feedback" id="feedback" class="form-control" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Select File(s)</label>
            <input type="file" id="file-input" class="form-control" multiple
                accept=".pdf,.doc,.docx,.ppt,.pptx,.jpg,.jpeg,.png,.gif">
        </div>

        <div id="preview-box"></div>

        <button type="submit" class="btn btn-primary mt-3">Upload Worksheet(s)</button>
    </form>
</div>

<div class="modal fade" id="createWeekModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create New Week</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form method="POST">
            <input type="text" class="form-control" name="new_week" placeholder="e.g., Week 6" required>
            <button class="btn btn-primary mt-3">Create Week</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
let allFiles = [];
const fileInput = document.getElementById('file-input');
const previewBox = document.getElementById('preview-box');

fileInput.addEventListener('change', function() {
    allFiles = allFiles.concat(Array.from(this.files));
    renderPreview();
});

function renderPreview() {
    previewBox.innerHTML = "";
    allFiles.forEach((file, index) => {
        const item = document.createElement("div");
        item.classList.add("preview-item");

        const ext = file.name.split('.').pop().toLowerCase();
        if (['jpg','jpeg','png','gif'].includes(ext)) {
            const img = document.createElement("img");
            img.src = URL.createObjectURL(file);
            item.appendChild(img);
        } else {
            const icon = document.createElement("div");
            icon.classList.add("file-icon");
            icon.innerHTML = "📄";
            item.appendChild(icon);
        }

        const label = document.createElement("div");
        label.textContent = file.name;
        item.appendChild(label);

        // Remove button
        const removeBtn = document.createElement("span");
        removeBtn.classList.add("remove-btn");
        removeBtn.textContent = "Remove";
        removeBtn.addEventListener('click', () => {
            allFiles.splice(index, 1);
            renderPreview();
        });
        item.appendChild(removeBtn);

        previewBox.appendChild(item);
    });
}

// AJAX submission
document.getElementById('upload-form').addEventListener('submit', function(e){
    e.preventDefault();
    if (allFiles.length === 0) {
        Swal.fire({icon:'error', title:'No files selected', text:'Please choose at least one file.'});
        return;
    }

    const formData = new FormData();
    formData.append('ajax_upload', 1);
    formData.append('child_id', document.getElementById('child_id').value);
    formData.append('title', document.getElementById('title').value);
    formData.append('week', document.getElementById('week').value);
    formData.append('feedback', document.getElementById('feedback').value);

    allFiles.forEach(file => formData.append('files[]', file));

    fetch('upload-worksheet.php', {
        method: 'POST',
        body: formData
    }).then(res => res.json()).then(data => {
        if(data.status === 'success'){
            Swal.fire({icon:'success', title:'Upload Complete', text:data.message}).then(()=>{
                allFiles = [];
                renderPreview();
                document.getElementById('upload-form').reset();
            });
        } else {
            Swal.fire({icon:'error', title:'Error', text:data.message});
        }
    }).catch(err=>{
        Swal.fire({icon:'error', title:'Upload Failed', text:'An error occurred.'});
        console.error(err);
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
