<?php
session_start();
include '../config/dbconnect.php';

// রোগী লগইন অবস্থায় আছে কিনা চেক করা
if (!isset($_SESSION['nid'])) {
    header("Location: login.php");
    exit();
}

$patient_nid = $_SESSION['nid'];

// ডাটাবেজ থেকে রোগীর লেটেস্ট প্রোফাইল ডাটা রিভ করা
$result = mysqli_query($conn, "SELECT * FROM patient WHERE NID='$patient_nid'");
$pat = mysqli_fetch_assoc($result);

include '../includes/patient_header.php';
?>

<div class="row mb-4 align-items-center bg-white p-4 rounded shadow-sm mx-1">
    <div class="col-md-2 text-center text-md-start mb-3 mb-md-0">
        <img src="../uploads/doctors/default_doctor.png" 
             alt="Patient Pic" 
             class="img-fluid rounded-circle border border-info border-3" 
             style="width: 100px; height: 100px; object-fit: cover;">
    </div>
    <div class="col-md-7">
        <h3 class="fw-bold text-dark mb-1">Welcome, <?php echo $pat['Full_Name']; ?></h3>
        <p class="text-info mb-1 fw-semibold"><i class="fa-solid fa-id-card me-1"></i> NID: <?php echo $pat['NID']; ?></p>
        <p class="text-muted small mb-0"><i class="fa-solid fa-phone me-1"></i> Contact: <?php echo isset($pat['Phone']) ? $pat['Phone'] : 'Not Provided'; ?></p>
    </div>
    <div class="col-md-3 text-center text-md-end">
        <span class="badge bg-success px-3 py-2 rounded-pill fs-6">
            <i class="fa-solid fa-circle-check me-1"></i> Account Active
        </span>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-dark">Patient <span class="text-info">Control Center</span></h2>
        <p class="text-muted">Access your digital prescriptions, view test results, and check appointment logs.</p>
    </div>
</div>

<div class="row g-4 mb-5">
    
    <div class="col-md-3">
        <div class="card menu-card p-4 position-relative">
            <div class="icon-box bg-patient-teal"><i class="fa-solid fa-notes-medical"></i></div>
            <h5 class="fw-bold">Medical History</h5>
            <p class="small text-muted">Review your previous digital prescriptions and doctor advice.</p>
            <a href="medical_history.php" class="stretched-link"></a>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card menu-card p-4 position-relative">
            <div class="icon-box bg-patient-blue"><i class="fa-solid fa-file-medical"></i></div>
            <h5 class="fw-bold">Pathology Reports</h5>
            <p class="small text-muted">View and download reports uploaded by medical technologists.</p>
            <a href="medical_history.php#pathology" class="stretched-link"></a>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card menu-card p-4 position-relative">
            <div class="icon-box bg-patient-purple"><i class="fa-solid fa-download"></i></div>
            <h5 class="fw-bold">E-Prescriptions</h5>
            <p class="small text-muted">Get easy access to your digital medication Slips securely.</p>
            <a href="medical_history.php" class="stretched-link"></a>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card menu-card p-4 position-relative">
            <div class="icon-box bg-patient-teal" style="background: rgba(40, 167, 69, 0.1); color: #28a745;"><i class="fa-solid fa-user-gear"></i></div>
            <h5 class="fw-bold">My Account</h5>
            <p class="small text-muted">View your personal registration credentials and safety logs.</p>
            <a href="#" class="stretched-link"></a>
        </div>
    </div>

</div>

<?php
include '../includes/footer.php';
?>