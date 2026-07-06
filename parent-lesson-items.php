<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'parent') {
    header("Location: login.php");
    exit;
}

$lesson_id = (int)($_GET['lesson_id'] ?? 0);

// Fetch lesson
$stmt = $conn->prepare("SELECT * FROM lessons WHERE id = ?");
$stmt->bind_param("i", $lesson_id);
$stmt->execute();
$lesson = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$lesson) die("Lesson not found.");

// Fetch lesson items
$stmt = $conn->prepare("SELECT * FROM lesson_items WHERE lesson_id = ? ORDER BY created_at ASC");
$stmt->bind_param("i", $lesson_id);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($lesson['lesson_name']) ?> - KiddiLearn</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="lib/animate/animate.min.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<style>
body { background-color: #fef8f9; font-family: 'Nunito', sans-serif; }

.card-alphabet {
    border-radius: 20px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    transition: 0.3s;
    cursor: pointer;
    background: rgb(250,220,230);
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
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
.card-img-top { max-width: 100%; max-height: 100%; object-fit: contain; }

.alphabet-title { font-size: 1.8rem; font-weight: bold; color: #e91e63; margin-top: 10px; }

.zoom-overlay {
    position: fixed;
    top: 0; left:0;
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
}
.zoom-content img { max-width: 500px; max-height: 500px; margin-bottom: 20px; object-fit: contain; }
.zoom-close { position: absolute; top: 10px; right: 15px; font-size: 1.8rem; cursor: pointer; background: transparent; border: none; }
.zoom-letter { font-size: 2rem; font-weight: bold; color: #e91e63; margin-bottom: 15px; }
.play-sound-btn { margin-top: 10px; }
</style>
</head>
<body>

<div class="container mt-4">
    <a href="parent-lessons.php" class="btn btn-primary mb-4">← Back to Lessons</a>
</div>

<div class="container">
  <div class="row g-4">
    <?php foreach ($items as $item): ?>
      <div class="col-6 col-md-4 col-lg-3">
        <div class="card-alphabet" onclick="openZoomModal('<?= htmlspecialchars($item['title']) ?>','<?= htmlspecialchars($item['image_path']) ?>','<?= htmlspecialchars($item['audio_path']) ?>')">
            <?php if(!empty($item['image_path'])): ?>
              <div class="card-img-container">
                  <img src="<?= htmlspecialchars($item['image_path']) ?>" class="card-img-top" alt="<?= htmlspecialchars($item['title']) ?>">
              </div>
            <?php endif; ?>
            <h3 class="alphabet-title"><?= htmlspecialchars($item['title']) ?></h3>
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
        <img id="zoomImage" src="" alt="Zoomed Item"><br>
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
function openZoomModal(title, imgSrc, audioSrc){
    document.getElementById('zoomLetter').innerText = title;
    document.getElementById('zoomImage').src = imgSrc;

    const playBtn = document.getElementById('playSoundBtn');
    const zoomAudio = document.getElementById('zoomAudio');

    if(audioSrc){ zoomAudio.src = audioSrc; playBtn.disabled = false; }
    else { zoomAudio.src = ''; playBtn.disabled = true; }

    document.getElementById('zoomOverlay').style.display = 'flex';
}

function closeZoomModal(){
    document.getElementById('zoomOverlay').style.display = 'none';
    const zoomAudio = document.getElementById('zoomAudio');
    zoomAudio.pause(); zoomAudio.currentTime = 0;
}

function playSound(){ document.getElementById('zoomAudio').play(); }
</script>

</body>
</html>
