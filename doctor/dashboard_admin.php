<?php
// ============================================
// doctor/dashboard_admin.php - অ্যাডমিন থেকে লগইন
// সরাসরি এই পেজ দেখাবে
// ============================================

session_start();
include '../config/dbconnect.php';

// ============================================
// 🔒 চেক করুন - অ্যাডমিন থেকে লগইন করেছেন কিনা
// ============================================
if (!isset($_SESSION['doctor_logged_in']) || $_SESSION['doctor_logged_in'] !== true) {
    // যদি লগইন না থাকে, তাহলে লগইন পেজে পাঠান
    header("Location: ../login.php");
    exit();
}

$doctor_nid = $_SESSION['doctor_nid'] ?? '';

// ডাটাবেজ থেকে ডাক্তারের ডাটা আনা
$result = mysqli_query($conn, "SELECT * FROM doctor WHERE NID='$doctor_nid'");
$doc = mysqli_fetch_assoc($result);

if (!$doc) {
    header("Location: ../login.php");
    exit();
}

include '../includes/doctor_header.php';
?>

<!-- ============================================ -->
<!-- অ্যাডমিন ড্যাশবোর্ড HTML -->
<!-- ============================================ -->

<div class="container mt-3">
    <!-- সেশন স্ট্যাটাস বার -->
    <div class="alert alert-success d-flex justify-content-between align-items-center">
        <div>
            <i class="fa-solid fa-circle text-success"></i>
            <strong>Admin Login:</strong> <?= htmlspecialchars($doc['Full_Name']) ?> 
            <span class="badge bg-primary ms-2">Admin Access</span>
        </div>
        <div>
            <span class="text-muted small me-3">
                <i class="fa-regular fa-clock"></i> 
                <?= date('h:i A', $_SESSION['doctor_login_time'] ?? time()) ?>
            </span>
            <button onclick="logoutDoctor()" class="btn btn-danger btn-sm">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </button>
        </div>
    </div>

    <!-- প্রোফাইল হেডার -->
    <div class="row mb-4 align-items-center bg-white p-4 rounded shadow-sm">
        <div class="col-md-2 text-center text-md-start mb-3 mb-md-0">
            <img src="../uploads/doctors/<?php echo !empty($doc['Profile_Pic']) ? $doc['Profile_Pic'] : 'default_doctor.png'; ?>" 
                 alt="Doctor Pic" 
                 class="img-fluid rounded-circle border border-primary border-3" 
                 style="width: 110px; height: 110px; object-fit: cover;">
        </div>
        <div class="col-md-7">
            <h3 class="fw-bold text-dark mb-1">
                <?php echo !empty($doc['Title']) ? $doc['Title'] . ' ' : ''; ?><?php echo $doc['Full_Name']; ?>
                <span class="badge bg-primary ms-2"><i class="fa-solid fa-key"></i> Admin</span>
            </h3>
            <p class="text-primary mb-1 fw-semibold"><i class="fa-solid fa-stethoscope me-1"></i> <?php echo $doc['Specialization']; ?></p>
            <p class="text-muted small mb-0"><i class="fa-solid fa-hospital me-1"></i> Chamber: <?php echo !empty($doc['Chamber']) ? $doc['Chamber'] : 'Not set yet'; ?></p>
        </div>
        <div class="col-md-3 text-center text-md-end">
            <a href="profile_edit.php" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="fa-solid fa-user-gear me-1"></i> Edit Profile
            </a>
        </div>
    </div>

    <!-- টাইটেল -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-dark">Admin <span class="text-primary">Access Panel</span></h2>
            <p class="text-muted">
                <i class="fa-solid fa-shield-halved text-primary"></i> 
                You are logged in via Admin Panel.
            </p>
        </div>
    </div>

    <!-- মেনু কার্ড -->
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card p-4 shadow-sm h-100">
                <div class="text-primary mb-2" style="font-size: 28px;"><i class="fa-solid fa-user-md"></i></div>
                <h5 class="fw-bold">Doctor Profile</h5>
                <p class="small text-muted">View your full doctor profile.</p>
                <a href="profile.php" class="btn btn-outline-primary btn-sm">Go</a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4 shadow-sm h-100">
                <div class="text-success mb-2" style="font-size: 28px;"><i class="fa-solid fa-file-prescription"></i></div>
                <h5 class="fw-bold">Digital Prescription</h5>
                <p class="small text-muted">Generate e-prescriptions.</p>
                <a href="prescription.php" class="btn btn-outline-success btn-sm">Go</a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4 shadow-sm h-100">
                <div class="text-purple mb-2" style="font-size: 28px; color: #6f42c1;"><i class="fa-solid fa-microscope"></i></div>
                <h5 class="fw-bold">Pathology Tests</h5>
                <p class="small text-muted">Assign medical tests.</p>
                <a href="pathology_test.php" class="btn btn-outline-secondary btn-sm">Go</a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4 shadow-sm h-100">
                <div class="text-warning mb-2" style="font-size: 28px;"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                <h5 class="fw-bold">Upload Reports</h5>
                <p class="small text-muted">Upload medical reports.</p>
                <a href="upload_report.php" class="btn btn-outline-warning btn-sm">Go</a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4 shadow-sm h-100">
                <div class="text-info mb-2" style="font-size: 28px;"><i class="fa-solid fa-user-injured"></i></div>
                <h5 class="fw-bold">Patient Records</h5>
                <p class="small text-muted">Search patient profiles.</p>
                <a href="patient_login_check.php" class="btn btn-outline-info btn-sm">Go</a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4 shadow-sm h-100">
                <div class="text-danger mb-2" style="font-size: 28px;"><i class="fa-solid fa-shield-halved"></i></div>
                <h5 class="fw-bold">Verify Report</h5>
                <p class="small text-muted">Verify report authenticity.</p>
                <a href="verify_report.php" class="btn btn-outline-danger btn-sm">Go</a>
            </div>
        </div>
        <!-- অ্যাডমিন এক্সট্রা -->
        <div class="col-md-3">
            <div class="card p-4 shadow-sm h-100 border border-primary">
                <div class="text-primary mb-2" style="font-size: 28px;"><i class="fa-solid fa-crown"></i></div>
                <h5 class="fw-bold">Admin Tools <span class="badge bg-primary">New</span></h5>
                <p class="small text-muted">Advanced admin tools.</p>
                <a href="#" class="btn btn-primary btn-sm">Go</a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4 shadow-sm h-100 border border-danger">
                <div class="text-danger mb-2" style="font-size: 28px;"><i class="fa-solid fa-chart-line"></i></div>
                <h5 class="fw-bold">System Logs <span class="badge bg-danger">Admin</span></h5>
                <p class="small text-muted">View system activity.</p>
                <a href="#" class="btn btn-outline-danger btn-sm">Go</a>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- JAVASCRIPT -->
<!-- ============================================ -->
<script>
    // ট্যাব বন্ধ করলে সেশন শেষ
    window.addEventListener('load', function() {
        if (!sessionStorage.getItem('doctor_session')) {
            sessionStorage.setItem('doctor_session', 'admin');
        }
        console.log('✅ Admin Doctor session active');
    });

    window.addEventListener('unload', function() {
        sessionStorage.removeItem('doctor_session');
        navigator.sendBeacon('logout_doctor_ajax.php');
    });

    document.addEventListener('visibilitychange', function() {
        if (!document.hidden && !sessionStorage.getItem('doctor_session')) {
            window.location.href = 'logout_doctor.php';
        }
    });

    setInterval(function() {
        if (!sessionStorage.getItem('doctor_session')) {
            window.location.href = 'logout_doctor.php';
        }
    }, 10000);

    function logoutDoctor() {
        if (confirm('Are you sure you want to logout?')) {
            sessionStorage.removeItem('doctor_session');
            window.location.href = 'logout_doctor.php';
        }
    }
</script>

<?php
include '../includes/footer.php';
?>