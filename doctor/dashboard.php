<?php
session_start();
include '../config/dbconnect.php'; // ডাটাবেজ কানেকশন নিশ্চিত করার জন্য

// ডাক্তার লগইন অবস্থায় আছে কিনা চেক করা
if (!isset($_SESSION['doctor'])) {
    header("Location: login.php");
    exit();
}

$doctor_nid = $_SESSION['doctor'];

// ডাটাবেজ থেকে ডাক্তারের লেটেস্ট প্রোফাইল ডাটা রিভ করা
$result = mysqli_query($conn, "SELECT * FROM doctor WHERE NID='$doctor_nid'");
$doc = mysqli_fetch_assoc($result);

include '../includes/doctor_header.php';
?>

<div class="row mb-4 align-items-center bg-white p-4 rounded shadow-sm mx-1">
    <div class="col-md-2 text-center text-md-start mb-3 mb-md-0">
        <img src="../uploads/doctors/<?php echo !empty($doc['Profile_Pic']) ? $doc['Profile_Pic'] : 'default_doctor.png'; ?>" 
             alt="Doctor Pic" 
             class="img-fluid rounded-circle border border-primary border-3" 
             style="width: 110px; height: 110px; object-fit: cover;">
    </div>
    <div class="col-md-7">
        <h3 class="fw-bold text-dark mb-1">
            <?php echo !empty($doc['Title']) ? $doc['Title'] . ' ' : ''; ?><?php echo $doc['Full_Name']; ?>
        </h3>
        <p class="text-primary mb-1 fw-semibold"><i class="fa-solid fa-stethoscope me-1"></i> <?php echo $doc['Specialization']; ?></p>
        <p class="text-muted small mb-0"><i class="fa-solid fa-hospital me-1"></i> Chamber: <?php echo !empty($doc['Chamber']) ? $doc['Chamber'] : 'Not set yet'; ?></p>
    </div>
    <div class="col-md-3 text-center text-md-end">
        <a href="profile_edit.php" class="btn btn-outline-primary btn-sm rounded-pill px-3">
            <i class="fa-solid fa-user-gear me-1"></i> Edit Full Profile
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-dark">Doctor <span class="text-primary">Control Center</span></h2>
        <p class="text-muted">Welcome back! Manage your patients and medical reports efficiently.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-3">
        <div class="card menu-card p-4 position-relative">
            <div class="icon-box bg-medical-blue"><i class="fa-solid fa-user-md"></i></div>
            <h5 class="fw-bold">Doctor Profile</h5>
            <p class="small text-muted">View your full doctor profile and all available information.</p>
            <a href="profile.php" class="stretched-link"></a> 
        </div>
    </div>

    <div class="col-md-3">
        <div class="card menu-card p-4">
            <div class="icon-box bg-medical-teal"><i class="fa-solid fa-file-prescription"></i></div>
            <h5 class="fw-bold">Digital Prescription</h5>
            <p class="small text-muted">Generate and issue new e-prescriptions for patients.</p>
            <a href="prescription.php" class="stretched-link"></a>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card menu-card p-4">
            <div class="icon-box bg-medical-blue"><i class="fa-solid fa-microscope"></i></div>
            <h5 class="fw-bold">Pathology Tests</h5>
            <p class="small text-muted">Select and assign medical tests for diagnosis.</p>
            <a href="pathology_test.php" class="stretched-link"></a>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card menu-card p-4">
            <div class="icon-box bg-medical-teal"><i class="fa-solid fa-vial-circle-check"></i></div>
            <h5 class="fw-bold">MT Portal Access</h5>
            <p class="small text-muted">Management & Technician login for report handling.</p>
            <a href="../mt/login.php" class="stretched-link"></a>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card menu-card p-4">
            <div class="icon-box bg-medical-blue"><i class="fa-solid fa-cloud-arrow-up"></i></div>
            <h5 class="fw-bold">Upload Reports</h5>
            <p class="small text-muted">Securely upload scanned reports and medical files.</p>
            <a href="upload_report.php" class="stretched-link"></a>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card menu-card p-4">
            <div class="icon-box bg-medical-teal"><i class="fa-solid fa-user-injured"></i></div>
            <h5 class="fw-bold">Patient Records</h5>
            <p class="small text-muted">Search and verify patient medical profiles.</p>
            <a href="patient_login_check.php" class="stretched-link"></a>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card menu-card p-4">
            <div class="icon-box bg-medical-blue"><i class="fa-solid fa-notes-medical"></i></div>
            <h5 class="fw-bold">Medical History</h5>
            <p class="small text-muted">Review previous treatments and history logs.</p>
            <a href="appointment_history.php" class="stretched-link"></a>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card menu-card p-4">
            <div class="icon-box bg-medical-teal" style="background: rgba(40, 167, 69, 0.1); color: #28a745;"><i class="fa-solid fa-shield-halved"></i></div>
            <h5 class="fw-bold">Verify Report</h5>
            <p class="small text-muted">Verify the authenticity of reports via Secure Hash.</p>
            <a href="verify_report.php" class="stretched-link"></a>
        </div>
    </div>
</div>

<?php
include '../includes/footer.php';
?>