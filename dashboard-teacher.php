<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

$teacher = $_SESSION['user'];
$profileUpdated = isset($_GET['updated']) && $_GET['updated'] === '1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard - KiddiLearn</title>
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
        .dashboard-card { border: 1px solid black; border-radius: 20px; padding: 40px 20px; text-align: center; box-shadow: 0 15px 1px rgba(0,0,0,0.05); transition: 0.3s; background-color: rgb(250, 248, 249); color: #333; text-decoration: none; display: block; height: 100%; }
        .dashboard-card:hover { background-color: #e91e63; color: white; transform: translateY(-5px); }
        .dashboard-card i { font-size: 32px; margin-bottom: 15px; display: block; transition: color 0.3s; }
        .dashboard-card:hover i { color: white !important; }
        .navbar .btn-primary { background-color: #e91e63; border-color: #e91e63; }
        .navbar .btn-primary:hover { background-color: rgb(25, 14, 187); border-color: #d81b60; }
        .profile-icon { font-size: 50px; color: #d81b60; cursor: pointer; transition: 0.3s; }
        .profile-icon:hover { color: #e91e63; }
        .dropdown-menu { border-radius: 10px; }
        .dashboard-card:hover h5 { color: white; }
        .teacher-list-item { cursor: pointer; transition: background .15s; display:flex; justify-content:space-between; align-items:center; }
        .teacher-list-item:hover { background: #f1f1f1; }
        .chat-bubble { padding: .6rem; border-radius: .5rem; margin-bottom: .5rem; display: inline-block; max-width:80%; word-wrap: break-word; }
        .chat-bubble.me { background: #0d6efd; color: #fff; align-self: flex-end; }
        .chat-bubble.they { background: #f1f1f1; color: #333; align-self: flex-start; }
.teacher-list-item.active {
    background-color: #e91e63 !important;
    color: white;
    border-radius: 5px;
}
.teacher-list-item.active strong,
.teacher-list-item.active small {
    color: white;
}

        #chatBody { flex: 1 1 auto; overflow-y: auto; }

        @media (max-width: 768px) { .modal-lg { max-width: 95%; } }

        /* Custom SweetAlert2 toast */
        .swal2-toast.custom-toast { top: 80px !important; }
        .custom-toast .swal2-timer-progress-bar { background-color: #d81b60 !important; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top px-4 shadow-sm">
    <h1 class="text-primary display-6">Kiddie<span class="text-secondary">Learn</span></h1>
    <div class="ms-auto d-flex align-items-center">
        <div class="dropdown">
            <a href="#" class="text-decoration-none" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user-circle profile-icon"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#settingsModal">Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="#" id="logoutBtn">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Dashboard Content -->
<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="display-6 text-primary wow fadeInDown" data-wow-delay="0.1s">Welcome, <?= htmlspecialchars($teacher['first_name'] ?? '') ?>!</h2>
        <p class="lead">Access your teaching tools below</p>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-md-6 col-lg-4">
            <a href="manage-lessons.php" class="dashboard-card wow fadeInUp" data-wow-delay="0.7s">
                <i class="fas fa-book-open text-primary"></i>
                <h5>Manage Lessons</h5>
            </a>
        </div>
        <div class="col-md-6 col-lg-4">
            <a href="teacher-grade-student.php" class="dashboard-card wow fadeInUp" data-wow-delay="0.5s">
                <i class="fas fa-clipboard-check text-success"></i>
                <h5>Grade Students</h5>
            </a>
        </div>
        <div class="col-md-6 col-lg-4">
            <a href="upload-worksheet.php" class="dashboard-card wow fadeInUp" data-wow-delay="0.6s">
                <i class="fas fa-upload text-success"></i>
                <h5>Upload Worksheet</h5>
            </a>
        </div>
        <div class="col-md-6 col-lg-4">
            <a href="student-activities.php" class="dashboard-card wow fadeInUp" data-wow-delay="0.4s">
                <i class="fas fa-clipboard-list text-warning"></i>
                <h5>Student Activities</h5>
            </a>
        </div>
    </div>
</div>

<!-- Settings Modal -->
<div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" id="profileForm" method="POST" action="update-profile.php">
      <div class="modal-header">
        <h5 class="modal-title" id="settingsModalLabel">Edit Profile</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="user_id" value="<?= $teacher['id'] ?>">
        <div class="mb-3">
          <label class="form-label">First Name</label>
          <input type="text" class="form-control" name="first_name" value="<?= htmlspecialchars($teacher['first_name'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Last Name</label>
          <input type="text" class="form-control" name="last_name" value="<?= htmlspecialchars($teacher['last_name'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($teacher['email'] ?? '') ?>">
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<!-- CHAT MODAL -->
<div class="modal fade" id="chatModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content" style="height:520px;">
      <div class="modal-header">
        <h5 class="modal-title">Messages</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-0 h-100 d-flex">
        <div class="row g-0 flex-fill h-100">
          <div class="col-4 border-end" id="userList" style="overflow-y:auto; background:#fafafa; max-height: 450px;">
            <div class="p-2 fw-bold text-center bg-light border-bottom">Parents</div>
          </div>
          <div class="col-8 d-flex flex-column h-100">
            <div id="chatHeader" class="p-3 border-bottom">
              <strong id="chatWithName">Select a parent</strong>
            </div>
            <div id="chatBody" class="flex-grow-1 overflow-auto p-3" style="background:#f8f9fa;">
              <p class="text-center text-muted mt-5">Select a parent to view messages</p>
            </div>
            <div class="p-3 border-top">
              <div class="d-flex">
                  <input type="text" id="chatInput" class="form-control me-2" placeholder="Type your message...">
                  <button class="btn btn-primary" id="sendBtn">Send</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Floating Chat Button -->
<button class="btn btn-success position-fixed d-flex align-items-center" id="chatBtn" style="bottom:20px; right:20px; z-index:1000;" data-bs-toggle="modal" data-bs-target="#chatModal">
  <i class="fas fa-comments me-1"></i> Chat
  <span id="chatBadge" class="badge bg-danger ms-1" style="display:none;">0</span>
</button>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="lib/wow/wow.min.js"></script>
<script src="lib/owlcarousel/owl.carousel.min.js"></script>
<script src="js/main.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script> new WOW().init(); </script>

<script>
const LOGGED_IN_ID = <?= (int)$teacher['id'] ?>;
let activeUserId = 0;
let toastVisible = false;

// Logout
document.getElementById('logoutBtn').addEventListener('click', function(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Are you sure?',
        text: "You will be logged out of your account.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e91e63',
        cancelButtonColor: '#aaa',
        confirmButtonText: 'Yes, logout'
    }).then((result) => {
        if(result.isConfirmed) window.location.href='logout.php';
    });
});

// Profile form
document.getElementById('profileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    Swal.fire({
        title: 'Save changes?',
        text: "Your profile will be updated.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#e91e63',
        cancelButtonColor: '#aaa',
        confirmButtonText: 'Yes, save it'
    }).then(function(result) { if(result.isConfirmed) form.submit(); });
});

<?php if ($profileUpdated): ?>
Swal.fire({ icon: 'success', title: 'Profile Updated!', text: 'Your changes have been saved successfully.', confirmButtonColor: '#e91e63' });
<?php endif; ?>

// Escape
function escapeHtml(s){if(!s)return'';return s.replace(/[&<>"'`=\/]/g,function(c){return{'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','/':'&#x2F;','`':'&#x60;','=':'&#x3D;'}[c];});}

// Load parents
function loadUserListOnce(){
    fetch('get-users.php?role=parent')
    .then(res=>res.json())
    .then(users=>{
        const list=document.getElementById('userList');
        list.innerHTML=`<div class="p-2 fw-bold text-center bg-light border-bottom">Parents</div>`;
        users.forEach(u=>{
            const div=document.createElement('div');
            div.className='p-3 border-bottom teacher-list-item';
            div.dataset.id=u.id;
            div.innerHTML=`<strong>${escapeHtml(u.first_name)} ${escapeHtml(u.last_name)}</strong>
                           <span class="badge bg-danger ms-1" id="badge-${u.id}" style="display:none;">0</span>
                           <br><small class="text-muted">Parent</small>`;
            div.onclick=()=>openConversation(u.id,`${u.first_name} ${u.last_name}`);
            list.appendChild(div);
        });
    });
}

// Open conversation
function openConversation(userId, name) {
    activeUserId = userId;
    document.getElementById('chatWithName').innerText = name;
    fetch(`mark-as-read.php?user_id=${userId}`);
    loadMessages();

    // Highlight selected user
    document.querySelectorAll('.teacher-list-item').forEach(item => {
        item.classList.remove('active');
    });
    const selectedItem = document.querySelector(`.teacher-list-item[data-id='${userId}']`);
    if (selectedItem) selectedItem.classList.add('active');
}


// Load messages
function loadMessages(){
    if(activeUserId<=0)return;
    fetch(`get-messages.php?user_id=${activeUserId}`)
    .then(res=>res.json())
    .then(data=>{
        const chatBody=document.getElementById('chatBody');
        chatBody.innerHTML='';
        data.forEach(msg=>{
            const div=document.createElement('div');
            const isMe=parseInt(msg.sender_id)===LOGGED_IN_ID;
            div.className='d-flex flex-column';
            const bubble=document.createElement('div');
            bubble.className='chat-bubble '+(isMe?'me':'they');
            const sentDate = new Date(msg.sent_at);
            const options = { year:'numeric', month:'short', day:'numeric', hour:'numeric', minute:'numeric', hour12:true };
            const formattedTime = sentDate.toLocaleString([], options);
            bubble.innerHTML = `<div>${escapeHtml(msg.message)}</div>
                                <div class="small mt-1" style="opacity:0.75;">${formattedTime}</div>`;
            if(isMe){bubble.style.marginLeft='auto';}else{bubble.style.marginRight='auto';}
            div.appendChild(bubble);
            chatBody.appendChild(div);
        });
        chatBody.scrollTop=chatBody.scrollHeight;
    });
}

// Send message
function sendMessage(){
    const textEl=document.getElementById('chatInput');
    const message=textEl.value.trim();
    if(!message||activeUserId<=0)return;
    fetch('send-message.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`receiver_id=${activeUserId}&message=${encodeURIComponent(message)}`})
    .then(()=>{textEl.value=''; loadMessages();});
}
document.getElementById('sendBtn').addEventListener('click',sendMessage);
document.getElementById('chatInput').addEventListener('keypress',function(e){if(e.key==='Enter'&&!e.shiftKey){e.preventDefault(); sendMessage();}});

// Update badges & persistent toast
function updateUnreadBadges(){
    fetch('get-unread-counts.php')
    .then(res=>res.json())
    .then(data=>{
        let totalUnread=0;
        for(const userId in data){
            const count=data[userId];
            const badgeEl=document.getElementById(`badge-${userId}`);
            if(badgeEl){ badgeEl.style.display=count>0?'inline-block':'none'; badgeEl.innerText=count; }
            totalUnread+=count;
        }
        const chatBadge=document.getElementById('chatBadge');
        if(chatBadge){ chatBadge.style.display=totalUnread>0?'inline-block':'none'; chatBadge.innerText=totalUnread; }

        if(totalUnread>0 && !toastVisible){
            toastVisible=true;
            Swal.fire({
                toast:true,
                position:'top-end',
                icon:'info',
                html:'<strong>You have new messages!</strong><br>Click the chat button below to open chat or click here.',
                showConfirmButton:false,
                background:'#e1f5fe',
                color:'#000',
                iconColor:'#0277bd',
                timer:3000,
                timerProgressBar:true,
                customClass:{popup:'custom-toast'},
                didClose:()=>{ toastVisible=false; }
            });
        }
    });
}

// Auto refresh
setInterval(()=>{ loadMessages(); updateUnreadBadges(); },2000);
document.getElementById('chatModal').addEventListener('shown.bs.modal',()=>{ loadUserListOnce(); });
</script>
</body>
</html>
