<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

$dir = "assets/alphabets/";
$letters = range('A', 'Z');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Learn Alphabet - KiddiLearn</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Favicon and Fonts -->
  <link href="img/favicon.ico" rel="icon">
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

  <!-- Stylesheets -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="lib/animate/animate.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #fef8f9;
      font-family: 'Nunito', sans-serif;
    }
    .card-alphabet {
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      transition: 0.3s;
      cursor: pointer;
    }
    .card-alphabet:hover {
      transform: scale(1.03);
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .card-img-top {
      height: 400px;
      object-fit: cover;
    }
    .alphabet-title {
      font-size: 2rem;
      font-weight: bold;
      color: #e91e63;
    }

    /* Zoom Modal */
    .zoom-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(0, 0, 0, 0.7);
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

    .zoom-content img {
      max-width: 300px;
      height: auto;
      margin-bottom: 20px;
    }

    .zoom-close {
      position: absolute;
      top: 10px;
      right: 15px;
      background: transparent;
      border: none;
      font-size: 1.8rem;
      color: #000;
      cursor: pointer;
    }

    .zoom-letter {
      font-size: 3rem;
      font-weight: bold;
      color: #e91e63;
    }
  </style>
</head>
<body>

<div class="container mt-4">
  <a href="manage-lessons.php" class="btn btn-primary px-4 py-2 btn-border-radius">
    ← Back to Lessons
  </a>
</div>

<div class="container text-center mb-5">
  <h1 class="display-4 wow fadeInDown" data-wow-delay="0.1s">
  <span class="text-primary" style="font-family: 'Pacifico', cursive;">Learn</span>
  <span class="text-secondary" style="font-family: 'Pacifico', cursive;">Alphabet</span>
</h1>
</div>

<div class="container">
  <div class="row g-4">
    <?php foreach ($letters as $letter): 
      $image = $dir . $letter . ".jpg";
    ?>
      <div class="col-6 col-md-4 col-lg-3 wow fadeInUp" data-wow-delay="0.1s">
        <div class="card card-alphabet text-center h-100 bg-light p-3" 
             onclick="openZoomModal('<?= $letter ?>', '<?= $image ?>')">
          <img src="<?= $image ?>" class="card-img-top" alt="<?= $letter ?>">
          <div class="card-body">
            <h3 class="alphabet-title"><?= $letter ?></h3>
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
    <img id="zoomImage" src="" alt="Zoomed Letter">
  </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="lib/wow/wow.min.js"></script>
<script>new WOW().init();</script>

<script>
  function openZoomModal(letter, imgSrc) {
    document.getElementById('zoomLetter').innerText = letter;
    document.getElementById('zoomImage').src = imgSrc;
    document.getElementById('zoomOverlay').style.display = 'flex';
  }

  function closeZoomModal() {
    document.getElementById('zoomOverlay').style.display = 'none';
  }
</script>

</body>
</html>
