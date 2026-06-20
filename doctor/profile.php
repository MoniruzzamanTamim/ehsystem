<?php
session_start();
include '../config/dbconnect.php';

// ডাক্তার লগইন অবস্থায় আছে কিনা চেক করা
if (!isset($_SESSION['doctor'])) {
    header("Location: login.php");
    exit();
}

$doctor_nid = $_SESSION['doctor'];

// ডাটাবেজ থেকে ডাক্তারের সম্পূর্ণ প্রোফাইল ডাটা রিভ করা
$result = mysqli_query($conn, "SELECT * FROM doctor WHERE NID='$doctor_nid'");
$doc = mysqli_fetch_assoc($result);

include '../includes/doctor_header.php';
?>

<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark">My <span class="text-primary">Professional Profile</span></h2>
                <p class="text-muted mb-0">This is how your profile appears to the system and patients.</p>
            </div>
            <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-center p-4 bg-white h-100">
                <div class="mb-3">
                    <img src="../uploads/doctors/<?php echo !empty($doc['Profile_Pic']) ? $doc['Profile_Pic'] : 'default_doctor.png'; ?>" 
                         alt="Doctor Picture" 
                         class="img-fluid rounded-circle border border-primary border-4 shadow-sm" 
                         style="width: 150px; height: 150px; object-fit: cover;">
                </div>
                <h4 class="fw-bold text-dark mb-1">
                    <?php echo !empty($doc['Title']) ? $doc['Title'] . ' ' : ''; ?><?php echo $doc['Full_Name']; ?>
                </h4>
                <p class="text-primary mb-2 fw-semibold"><i class="fa-solid fa-stethoscope me-1"></i> <?php echo $doc['Specialization']; ?></p>
                <div class="mb-3">
                    <span class="badge bg-success px-3 py-2 rounded-pill">Status: <?php echo $doc['Status']; ?></span>
                </div>
                <hr>
                <div class="text-start small text-muted">
                    <p class="mb-2"><strong><i class="fa-solid fa-id-card me-2"></i> NID:</strong> <?php echo $doc['NID']; ?></p>
                    <p class="mb-2"><strong><i class="fa-solid fa-envelope me-2"></i> Email:</strong> <?php echo $doc['Email']; ?></p>
                    <p class="mb-0"><strong><i class="fa-solid fa-phone me-2"></i> Phone:</strong> <?php echo $doc['Phone']; ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0 p-4 bg-white">
                
                <div class="mb-4">
                    <h5 class="fw-bold text-dark border-bottom pb-2"><i class="fa-solid fa-address-card text-primary me-2"></i> Bio / About Me</h5>
                    <p class="text-muted"><?php echo !empty($doc['Bio']) ? nl2br($doc['Bio']) : '<em class="text-danger">Not added yet.</em>'; ?></p>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <h5 class="fw-bold text-dark border-bottom pb-2"><i class="fa-solid fa-graduation-cap text-primary me-2"></i> Education & Degrees</h5>
                        <p class="text-muted small"><?php echo !empty($doc['Education']) ? nl2br($doc['Education']) : '<em class="text-danger">Not added yet.</em>'; ?></p>
                    </div>
                    <div class="col-md-6">
                        <h5 class="fw-bold text-dark border-bottom pb-2"><i class="fa-solid fa-briefcase text-primary me-2"></i> Experience</h5>
                        <p class="text-muted small"><?php echo !empty($doc['Experience']) ? nl2br($doc['Experience']) : '<em class="text-danger">Not added yet.</em>'; ?></p>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <h5 class="fw-bold text-dark border-bottom pb-2"><i class="fa-solid fa-hospital-user text-primary me-2"></i> Chamber Details</h5>
                        <p class="text-muted small"><?php echo !empty($doc['Chamber']) ? $doc['Chamber'] : '<em class="text-danger">Not added yet.</em>'; ?></p>
                    </div>
                    <div class="col-md-6">
                        <h5 class="fw-bold text-dark border-bottom pb-2"><i class="fa-solid fa-clock text-primary me-2"></i> Available Time</h5>
                        <p class="text-muted small"><?php echo !empty($doc['Available_Time']) ? $doc['Available_Time'] : '<em class="text-danger">Not added yet.</em>'; ?></p>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <h5 class="fw-bold text-dark border-bottom pb-2"><i class="fa-solid fa-file-contract text-primary me-2"></i> Registration & Certifications</h5>
                        <p class="text-muted small"><?php echo !empty($doc['Registration_Cert']) ? nl2br($doc['Registration_Cert']) : '<em class="text-danger">Not added yet.</em>'; ?></p>
                    </div>
                    <div class="col-md-6">
                        <h5 class="fw-bold text-dark border-bottom pb-2"><i class="fa-solid fa-star text-primary me-2"></i> Services & Expertise</h5>
                        <p class="text-muted small"><?php echo !empty($doc['Services_Expertise']) ? nl2br($doc['Services_Expertise']) : '<em class="text-danger">Not added yet.</em>'; ?></p>
                    </div>
                </div>

                <div class="mb-4">
                    <h5 class="fw-bold text-dark border-bottom pb-2"><i class="fa-solid fa-book-bookmark text-primary me-2"></i> Research & Publications</h5>
                    <p class="text-muted small"><?php echo !empty($doc['Research_Pub']) ? nl2br($doc['Research_Pub']) : '<em class="text-danger">Not added yet.</em>'; ?></p>
                </div>

                <div class="d-flex justify-content-end gap-2 border-top pt-3">
                    <a href="dashboard.php" class="btn btn-light rounded-pill px-4">
                        <i class="fa-solid fa-house me-1"></i> Dashboard
                    </a>
                    <a href="profile_edit.php" class="btn btn-primary rounded-pill px-4">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit Profile Info
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<?php
include '../includes/footer.php';
?>