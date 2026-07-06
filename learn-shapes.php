<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

$shapes = [
    'Circle', 'Square', 'Triangle', 'Rectangle',
    'Star', 'Heart', 'Oval', 'Diamond'
];
$dir = "assets/shapes/";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Learn Shapes - KiddiLearn</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Template CSS -->
  <link href="img/favicon.ico" rel="icon">
  <link href="lib/animate/animate.min.css" rel="stylesheet">
  <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #fef8f9;
    }
    .card-shape {
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      transition: 0.3s;
      cursor: pointer;
    }
    .card-shape:hover {
      transform: scale(1.03);
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .card-img-top {
      height: 400px;
      object-fit: cover;
    }
    .shape-title {
      font-size: 2rem;
      font-weight: bold;
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
    .zoom-title {
      font-size: 3rem;
      font-weight: bold;
      text-transform: capitalize;
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

<!-- Title -->
<div class="container text-center mb-5">
  <h1 class="display-4 wow fadeInDown" data-wow-delay="0.1s">
    <span class="text-primary" style="font-family: 'Pacifico', cursive;">Learn</span>
    <span class="text-secondary" style="font-family: 'Pacifico', cursive;">Shapes</span>
  </h1>
</div>

<!-- Shapes Grid -->
<div class="container">
  <div class="row g-4">
    <?php foreach ($shapes as $shape): 
      $image = $dir . $shape . ".jpg";
    ?>
      <div class="col-6 col-md-4 col-lg-3 wow fadeInUp" data-wow-delay="0.1s">
        <div class="card card-shape text-center h-100 bg-light p-3"
             onclick="openZoomModal('<?= $shape ?>', '<?= $image ?>')">
          <img src="<?= $image ?>" class="card-img-top" alt="<?= $shape ?>">
          <div class="card-body">
            <h3 class="shape-title text-dark"><?= $shape ?></h3>
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
    <h2 id="zoomTitle" class="zoom-title"></h2>
    <img id="zoomImage" src="" alt="Zoomed Shape">
  </div>
</div>

<!-- JS Libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="lib/wow/wow.min.js"></script>
<script src="lib/owlcarousel/owl.carousel.min.js"></script>
<script src="js/main.js"></script>
<script>new WOW().init();</script>

<script>
  function openZoomModal(title, imgSrc) {
    document.getElementById('zoomTitle').innerText = title;
    document.getElementById('zoomImage').src = imgSrc;
    document.getElementById('zoomOverlay').style.display = 'flex';
  }

  function closeZoomModal() {
    document.getElementById('zoomOverlay').style.display = 'none';
  }
</script>

</body>
</html>
