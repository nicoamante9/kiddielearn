<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'parent') {
    header("Location: login.php");
    exit;
}

$parent_id = $_SESSION['user']['id'];
$conn = new mysqli("localhost", "root", "", "kiddielearn");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle saving painted image
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['paintedImage']) && isset($_POST['worksheet_id']) && isset($_POST['action'])) {
    $imgData = $_POST['paintedImage'];
    $worksheet_id = (int)$_POST['worksheet_id'];
    $action = $_POST['action']; // save or save_submit

    $imgData = str_replace('data:image/png;base64,', '', $imgData);
    $imgData = str_replace(' ', '+', $imgData);
    $data = base64_decode($imgData);

    $savePath = "uploads/worksheets/painted_" . time() . "_" . $worksheet_id . ".png";
    file_put_contents($savePath, $data);

    // Update database with painted file
    $stmt = $conn->prepare("UPDATE worksheets SET painted_file_path = ? WHERE id = ?");
    $stmt->bind_param("si", $savePath, $worksheet_id);
    $stmt->execute();

    // Redirect if Save & Submit
    if($action === 'save_submit'){
        echo json_encode(['redirect' => "upload-activity.php?painted=" . urlencode($savePath)]);
        exit;
    }

    echo json_encode(['success' => true]);
    exit;
}

// Get all children of this parent
$children_stmt = $conn->prepare("SELECT * FROM children WHERE parent_id = ?");
$children_stmt->bind_param("i", $parent_id);
$children_stmt->execute();
$children_result = $children_stmt->get_result();

$worksheets_by_child = [];

while ($child = $children_result->fetch_assoc()) {
    $child_id = $child['id'];
    $worksheet_stmt = $conn->prepare("SELECT * FROM worksheets WHERE child_id = ?");
    $worksheet_stmt->bind_param("i", $child_id);
    $worksheet_stmt->execute();
    $worksheets_result = $worksheet_stmt->get_result();

    $worksheets_by_week = [];
    while ($ws = $worksheets_result->fetch_assoc()) {
        $week = $ws['week'];
        if (!isset($worksheets_by_week[$week])) $worksheets_by_week[$week] = [];
        $worksheets_by_week[$week][] = $ws;
    }

    // Sort weeks like Week 1, Week 2, etc.
    uksort($worksheets_by_week, function($a, $b){
        preg_match('/\d+/', $a, $a_num);
        preg_match('/\d+/', $b, $b_num);
        return intval($a_num[0] ?? 0) - intval($b_num[0] ?? 0);
    });

    $worksheets_by_child[$child['first_name']] = $worksheets_by_week;
    $worksheet_stmt->close();
}

$children_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>All Worksheets | KiddieLearn</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
th, td { white-space: nowrap; text-overflow: ellipsis; overflow: hidden; }
.paint-controls { margin-bottom:10px; }
#paintCanvas { border:1px solid #ccc; display:block; margin:auto; }
.scroll-container { max-height:70vh; overflow:auto; display:flex; justify-content:center; align-items:center; text-align:center; }
.modal-fullscreen .scroll-container { max-height:none; }
</style>
</head>
<body class="bg-light">
<div class="container mt-4">
<a href="dashboard-parent.php" class="btn btn-primary px-4 py-2 btn-border-radius">← Back to Dashboard</a>
</div>

<div class="container py-5">
<h2 class="text-center mb-4">📚 Worksheets for All Children</h2>

<?php if(count($worksheets_by_child) > 0): ?>
    <?php foreach($worksheets_by_child as $child_name => $weeks): ?>
        <div class="card mb-4">
            <div class="card-header bg-primary text-white"><?= htmlspecialchars($child_name) ?>'s Worksheets</div>
            <div class="card-body">
                <?php if(count($weeks) > 0): ?>
                    <?php foreach($weeks as $week => $worksheets): ?>
                        <h5 class="mt-3"><?= htmlspecialchars($week) ?></h5>
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered bg-white mb-0">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Uploaded At</th>
                                        <th>Feedback</th>
                                        <th>View</th>
                                        <th>Download</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($worksheets as $ws):
                                        $file_ext = strtolower(pathinfo($ws['file_path'], PATHINFO_EXTENSION));
                                        $file_path = $ws['painted_file_path'] ?? "uploads/worksheets/" . $ws['file_path'];
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($ws['title']) ?></td>
                                        <td><?= date("F j, Y", strtotime($ws['uploaded_at'])) ?></td>
                                        <td><?= htmlspecialchars($ws['feedback']) ?></td>
                                        <td class="text-center">
                                            <button class="btn btn-info btn-sm" onclick="viewFile('<?= htmlspecialchars($file_path) ?>','<?= $file_ext ?>', <?= $ws['id'] ?>)">View</button>
                                        </td>
                                        <td><a href="<?= htmlspecialchars($file_path) ?>" class="btn btn-primary btn-sm" download>Download</a></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-warning mb-0">No worksheets uploaded for this child yet.</div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="alert alert-info">You haven’t added any children yet.</div>
<?php endif; ?>
</div>

<!-- Modal -->
<div class="modal fade" id="filePreviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Preview</h5>
        <button type="button" id="fullscreenBtn" class="btn btn-secondary btn-sm me-2" onclick="toggleFullScreen()">Full Screen</button>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <div class="scroll-container" id="modal-body-content"></div>
      </div>
      <div class="modal-footer">
        <div id="paint-controls" class="paint-controls d-none">
            <input type="color" id="paintColor" value="#ff0000">
            <input type="range" id="brushSize" min="1" max="20" value="3">
            <button class="btn btn-secondary btn-sm" onclick="clearCanvas()">Clear</button>
            <button class="btn btn-success btn-sm" onclick="confirmSave('save')">Save</button>
            <button class="btn btn-warning btn-sm" onclick="confirmSave('save_submit')">Save & Submit</button>
        </div>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
let canvas, ctx, painting=false, imgLayer, currentWorksheetId, fullscreen=false;

function viewFile(path, ext, worksheetId){
    const modalBody = document.getElementById('modal-body-content');
    const paintControls = document.getElementById('paint-controls');
    modalBody.innerHTML=''; paintControls.classList.add('d-none'); canvas=null; ctx=null; imgLayer=null; currentWorksheetId = worksheetId;

    const imageTypes=['jpg','jpeg','png','gif'];
    const documentTypes=['pdf','doc','docx','ppt','pptx'];

    if(imageTypes.includes(ext)){
        const img=new Image();
        img.src=path;
        img.onload=function(){
            const maxWidth=window.innerWidth*0.8;
            const maxHeight=window.innerHeight*0.8;
            let scale=Math.min(maxWidth/img.width, maxHeight/img.height, 1);
            canvas=document.createElement('canvas');
            canvas.id='paintCanvas';
            canvas.width=img.width*scale;
            canvas.height=img.height*scale;
            ctx=canvas.getContext('2d');
            ctx.drawImage(img,0,0,canvas.width,canvas.height);
            imgLayer=ctx.getImageData(0,0,canvas.width,canvas.height);
            modalBody.appendChild(canvas);
            paintControls.classList.remove('d-none');
            canvas.onmousedown=()=>painting=true;
            canvas.onmouseup=()=>{painting=false;ctx.beginPath();};
            canvas.onmouseout=()=>{painting=false;ctx.beginPath();};
            canvas.onmousemove=draw;
        }
    } else if(documentTypes.includes(ext)){
        const iframe=document.createElement('iframe');
        iframe.src='https://docs.google.com/gview?url='+encodeURIComponent(window.location.origin+'/'+path)+'&embedded=true';
        iframe.width='100%'; iframe.height='600px';
        modalBody.appendChild(iframe);
    } else { modalBody.innerHTML='<p>Cannot preview this file type.</p>'; }

    const modal=new bootstrap.Modal(document.getElementById('filePreviewModal'));
    modal.show();
}

function draw(e){
    if(!painting || !ctx) return;
    const rect=canvas.getBoundingClientRect();
    ctx.strokeStyle=document.getElementById('paintColor').value;
    ctx.lineWidth=document.getElementById('brushSize').value;
    ctx.lineCap='round';
    ctx.lineTo(e.clientX-rect.left, e.clientY-rect.top);
    ctx.stroke();
    ctx.beginPath();
    ctx.moveTo(e.clientX-rect.left, e.clientY-rect.top);
}

function clearCanvas(){
    if(ctx && canvas) ctx.putImageData(imgLayer,0,0);
}

function confirmSave(action){
    Swal.fire({
        title: 'Are you sure?',
        text: "Do you want to save your changes?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, save it!',
    }).then((result)=>{
        if(result.isConfirmed) saveCanvas(action);
    });
}

function saveCanvas(action){
    if(!canvas) return;
    const dataURL=canvas.toDataURL('image/png');

    fetch('',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:new URLSearchParams({
            paintedImage: dataURL,
            worksheet_id: currentWorksheetId,
            action: action
        })
    }).then(res=>res.json())
      .then(data=>{
        if(action==='save_submit' && data.redirect){
            window.location.href=data.redirect;
        } else {
            Swal.fire('Saved!','Your annotated image has been saved.','success');
        }
    });
}

// Toggle fullscreen modal
function toggleFullScreen(){
    const modalDialog=document.querySelector('#filePreviewModal .modal-dialog');
    fullscreen=!fullscreen;
    modalDialog.classList.toggle('modal-fullscreen');
    document.getElementById('fullscreenBtn').innerText = fullscreen ? 'Minimize' : 'Full Screen';
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
