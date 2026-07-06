<?php
session_start();
require_once 'db.php';

// Only teachers allowed
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
    die("Lesson not found or you do not have permission.");
}

// Fetch lesson items
$stmt = $conn->prepare("SELECT * FROM lesson_items WHERE lesson_id = ? ORDER BY created_at ASC"); // changed DESC -> ASC
$stmt->bind_param("i", $lesson_id);
$stmt->execute();
$result = $stmt->get_result();
$items = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Lesson: <?= htmlspecialchars($lesson['lesson_name']) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="img/favicon.ico" rel="icon">
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="lib/animate/animate.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

  <style>
    body { background-color: #fef8f9; font-family: 'Nunito', sans-serif; }

    /* Enlarged, light pink card */
    .card-alphabet {
      border-radius: 20px;
      box-shadow: 0 6px 18px rgba(0,0,0,0.08);
      transition: 0.3s;
      cursor: pointer;
      background:rgb(250, 220, 230); /* light pink background */
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
      align-items: center;
      width: 320px;     
      padding: 15px;
    }
    .card-alphabet:hover {
      transform: scale(1.05);
      box-shadow: 0 12px 28px rgba(0,0,0,0.15);
    }

    .card-img-container {
      width: 100%;
      height: 280px;       
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
      border-radius: 15px;
      background-color: #fef8f9;
      margin-bottom: 15px;
    }

    .card-img-top {
      max-width: 100%;
      max-height: 100%;
      object-fit: contain;
    }

    .alphabet-title {
      font-size: 1.8rem;
      font-weight: bold;
      color: #e91e63;
      margin-top: 10px;
    }

    .card-footer {
      display: flex;
      justify-content: space-between;
      margin-top: 10px;
    }

    /* Zoom Modal */
    .zoom-overlay {
      position: fixed;
      top: 0; left: 0;
      width: 100vw; height: 100vh;
      background: rgba(0,0,0,0.7);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }
    .zoom-content {
      background: #fff;
      border-radius: 20px;
      padding: 20px;
      max-width: 90%;
      max-height: 90%;
      text-align: center;
      position: relative;
      box-shadow: 0 10px 25px rgba(0,0,0,0.3);
      animation: zoomIn 0.3s ease-in-out;
    }
    @keyframes zoomIn {
      from { transform: scale(0.7); opacity: 0; }
      to { transform: scale(1); opacity: 1; }
    }
    .zoom-content img { max-width: 500px; max-height: 500px; margin-bottom: 20px; object-fit: contain; }
    .zoom-close {
      position: absolute; top: 10px; right: 15px;
      background: transparent; border: none; font-size: 1.8rem; cursor: pointer;
    }
    .zoom-letter { font-size: 2rem; font-weight: bold; color: #e91e63; margin-bottom: 15px; }
    .play-sound-btn { margin-top: 10px; }
  </style>
</head>
<body>

<div class="container mt-4">
  <a href="manage-lessons.php" class="btn btn-primary px-4 py-2 btn-border-radius">
    ← Back to Lessons
  </a>
  <a href="add-lesson-item.php?lesson_id=<?= $lesson_id ?>" class="btn btn-success px-4 py-2 float-end">
    Add Item
  </a>
</div>

<div class="container text-center mb-5">
  <h1 class="display-4 wow fadeInDown" data-wow-delay="0.1s">
    <span class="text-primary" style="font-family: 'Pacifico', cursive;"><?= htmlspecialchars(explode(' ', $lesson['lesson_name'])[0]) ?></span>
    <span class="text-secondary" style="font-family: 'Pacifico', cursive;"><?= htmlspecialchars(implode(' ', array_slice(explode(' ', $lesson['lesson_name']), 1))) ?></span>
  </h1>
</div>

<div class="container">
  <div class="row g-4" style="flex-wrap: wrap;"> <!-- removed justify-content-center -->
    <?php foreach ($items as $item): ?>
      <div class="col-6 col-md-4 col-lg-3 wow fadeInUp" data-wow-delay="0.1s" id="item-card-<?= $item['id'] ?>">
        <div class="card card-alphabet h-100 p-3 text-center">
          <?php if (!empty($item['image_path'])): ?>
            <div class="card-img-container">
              <img src="<?= htmlspecialchars($item['image_path']) ?>" 
                   class="card-img-top" 
                   alt="<?= htmlspecialchars($item['title']) ?>"
                   onclick="openZoomModal('<?= htmlspecialchars($item['title']) ?>', '<?= htmlspecialchars($item['image_path']) ?>', '<?= htmlspecialchars($item['audio_path']) ?>')">
            </div>
          <?php endif; ?>
          <div class="card-body d-flex flex-column justify-content-between">
            <h3 class="alphabet-title"><?= htmlspecialchars($item['title']) ?></h3>
            <!-- Audio removed from card itself -->
          </div>
          <div class="card-footer d-flex justify-content-between">
  <a href="edit-lesson-item.php?id=<?= $item['id'] ?>" 
     class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1">
     <i class="fa fa-edit"></i> Edit
  </a>
  <button class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1" 
          onclick="deleteItem(<?= $item['id'] ?>, <?= $lesson_id ?>)">
     <i class="fa fa-trash"></i> Delete
  </button>
</div>

        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- Zoom Modal -->
<div id="zoomOverlay" class="zoom-overlay">
  <div class="zoom-content">
    <button class="zoom-close" onclick="closeZoomModal()">❌</button>
    <h2 id="zoomLetter" class="zoom-letter"></h2>
    <img id="zoomImage" src="" alt="Zoomed Item">
    <br>
    <button id="playSoundBtn" class="btn btn-primary play-sound-btn" onclick="playSound()" disabled>Play Sound 🔊</button>
    <audio id="zoomAudio" src="" style="display:none;"></audio>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="lib/wow/wow.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>new WOW().init();</script>

<script>
function openZoomModal(title, imgSrc, audioSrc) {
  document.getElementById('zoomLetter').innerText = title;
  document.getElementById('zoomImage').src = imgSrc;

  const playBtn = document.getElementById('playSoundBtn');
  const zoomAudio = document.getElementById('zoomAudio');

  if (audioSrc) {
    zoomAudio.src = audioSrc;
    playBtn.disabled = false;
  } else {
    zoomAudio.src = '';
    playBtn.disabled = true;
  }

  document.getElementById('zoomOverlay').style.display = 'flex';
}

function closeZoomModal() {
  document.getElementById('zoomOverlay').style.display = 'none';
  const zoomAudio = document.getElementById('zoomAudio');
  zoomAudio.pause();
  zoomAudio.currentTime = 0;
}

function playSound() {
  const zoomAudio = document.getElementById('zoomAudio');
  zoomAudio.play();
}

function deleteItem(itemId, lessonId) {
  Swal.fire({
    title: 'Are you sure?',
    text: "This will permanently delete the item!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#e91e63',
    cancelButtonColor: '#aaa',
    confirmButtonText: 'Yes, delete it!'
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: `delete-lesson-item.php?id=${itemId}&lesson_id=${lessonId}`,
        method: 'GET',
        success: function() {
          Swal.fire({
            icon: 'success',
            title: 'Deleted!',
            text: 'The lesson item has been deleted.',
            confirmButtonColor: '#e91e63'
          }).then(() => {
            document.getElementById(`item-card-${itemId}`).remove();
          });
        },
        error: function() {
          Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Failed to delete the item.',
            confirmButtonColor: '#e91e63'
          });
        }
      });
    }
  });
}
</script>

</body>
</html>
