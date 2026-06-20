<?php
session_start();
include '../config/dbconnect.php';

// ডাক্তার লগইন অবস্থায় আছে কিনা চেক করা
if (!isset($_SESSION['doctor'])) {
    header("Location: login.php");
    exit();
}

$doctor_nid = $_SESSION['doctor'];
$msg = "";
$msg_class = "";

// Form Submission Logic
if (isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $specialization = mysqli_real_escape_string($conn, $_POST['specialization']);
    $education = mysqli_real_escape_string($conn, $_POST['education']);
    $available_time = mysqli_real_escape_string($conn, $_POST['available_time']);
    $experience = mysqli_real_escape_string($conn, $_POST['experience']);
    $chamber = mysqli_real_escape_string($conn, $_POST['chamber']);
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);
    $reg_cert = mysqli_real_escape_string($conn, $_POST['reg_cert']);
    $services = mysqli_real_escape_string($conn, $_POST['services']);
    $research = mysqli_real_escape_string($conn, $_POST['research']);

    // Handle Profile Picture Upload
    $pic_query = "";
    if (!empty($_FILES['profile_pic']['name'])) {
        $pic_name = time() . '_' . basename($_FILES['profile_pic']['name']);
        $target_dir = "../uploads/doctors/";
        
        // ডিরেক্টরি না থাকলে তৈরি করবে
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_dir . $pic_name)) {
            $pic_query = ", Profile_Pic='$pic_name'";
        }
    }

    // Update Query
    $update_sql = "UPDATE doctor SET 
                    Full_Name='$name', 
                    Title='$title', 
                    Specialization='$specialization', 
                    Education='$education', 
                    Available_Time='$available_time', 
                    Experience='$experience', 
                    Chamber='$chamber', 
                    Bio='$bio', 
                    Registration_Cert='$reg_cert', 
                    Services_Expertise='$services', 
                    Research_Pub='$research' 
                    $pic_query 
                  WHERE NID='$doctor_nid'";

    if (mysqli_query($conn, $update_sql)) {
        $msg = "Profile updated successfully!";
        $msg_class = "alert-success";
    } else {
        $msg = "Error updating profile: " . mysqli_error($conn);
        $msg_class = "alert-danger";
    }
}

// Fetch Current Doctor Data (সব ফিল্ড সেভ বা লোড করার জন্য)
$result = mysqli_query($conn, "SELECT * FROM doctor WHERE NID='$doctor_nid'");
$doc = mysqli_fetch_assoc($result);

include '../includes/doctor_header.php';
?>

<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark">Edit <span class="text-primary">Profile Info</span></h2>
                <p class="text-muted mb-0">Update your professional details and profile presentation.</p>
            </div>
            <div class="gap-2 d-flex">
                <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill btn-sm px-3">
                    <i class="fa-solid fa-house me-1"></i> Dashboard
                </a>
                <a href="profile.php" class="btn btn-outline-primary rounded-pill btn-sm px-3">
                    <i class="fa-solid fa-user me-1"></i> View Profile
                </a>
            </div>
        </div>
    </div>

    <?php if (!empty($msg)): ?>
        <div class="alert <?php echo $msg_class; ?> alert-dismissible fade show" role="alert">
            <strong><i class="fa-solid fa-circle-check me-2"></i></strong> <?php echo $msg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 p-4 bg-white">
        <form method="POST" enctype="multipart/form-data">
            
            <div class="row align-items-center mb-4 bg-light p-3 rounded">
                <div class="col-md-2 text-center">
                    <img src="../uploads/doctors/<?php echo !empty($doc['Profile_Pic']) ? $doc['Profile_Pic'] : 'default_doctor.png'; ?>" 
                         alt="Current Profile Pic" 
                         class="img-fluid rounded-circle border border-secondary p-1" 
                         style="width: 110px; height: 110px; object-fit: cover;">
                </div>
                <div class="col-md-10 mt-3 mt-md-0">
                    <label class="form-label fw-bold"><i class="fa-solid fa-image text-primary me-1"></i> Change Profile Picture</label>
                    <input type="file" name="profile_pic" class="form-control">
                    <div class="form-text">Supported formats: JPG, PNG, JPEG. Max file size recommended: 2MB.</div>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?php echo isset($doc['Full_Name']) ? $doc['Full_Name'] : ''; ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Title (e.g., Prof. Dr. / Assistant Consultant)</label>
                    <input type="text" name="title" class="form-control" value="<?php echo isset($doc['Title']) ? $doc['Title'] : ''; ?>" placeholder="e.g., Dr. / Asst. Prof. Dr.">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Specialization <span class="text-danger">*</span></label>
                    <input type="text" name="specialization" class="form-control" value="<?php echo isset($doc['Specialization']) ? $doc['Specialization'] : ''; ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Available Time Slots</label>
                    <input type="text" name="available_time" class="form-control" value="<?php echo isset($doc['Available_Time']) ? $doc['Available_Time'] : ''; ?>" placeholder="e.g., Sat-Thu, 4 PM - 8 PM">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-12">
                    <label class="form-label fw-bold">Chamber Details & Address</label>
                    <input type="text" name="chamber" class="form-control" value="<?php echo isset($doc['Chamber']) ? $doc['Chamber'] : ''; ?>" placeholder="e.g., Room 402, Green Life Hospital, Dhaka">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Bio / About Me</label>
                <textarea name="bio" class="form-control" rows="3" placeholder="Briefly write about yourself..."><?php echo isset($doc['Bio']) ? $doc['Bio'] : ''; ?></textarea>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Education & Medical Degrees</label>
                    <textarea name="education" class="form-control" rows="3" placeholder="e.g., MBBS (DMC), FCPS (Medicine)"><?php echo isset($doc['Education']) ? $doc['Education'] : ''; ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Experience Details</label>
                    <textarea name="experience" class="form-control" rows="3" placeholder="e.g., 10+ Years experience in Cardiology Dept, BSMMU"><?php echo isset($doc['Experience']) ? $doc['Experience'] : ''; ?></textarea>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Registration & Certification</label>
                    <textarea name="reg_cert" class="form-control" rows="3" placeholder="e.g., BMDC Registration No: A-12345"><?php echo isset($doc['Registration_Cert']) ? $doc['Registration_Cert'] : ''; ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Services & Medical Expertise</label>
                    <textarea name="services" class="form-control" rows="3" placeholder="e.g., Hypertension control, Asthma treatment, Health Checkups"><?php echo isset($doc['Services_Expertise']) ? $doc['Services_Expertise'] : ''; ?></textarea>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Research & Publications</label>
                <textarea name="research" class="form-control" rows="3" placeholder="List your journals, research or thesis works..."><?php echo isset($doc['Research_Pub']) ? $doc['Research_Pub'] : ''; ?></textarea>
            </div>

            <div class="d-flex justify-content-end gap-2 border-top pt-3">
                <a href="profile.php" class="btn btn-light rounded-pill px-4">Cancel</a>
                <button type="submit" name="update_profile" class="btn btn-primary rounded-pill px-4">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
                </a>
            </div>
        </form>
    </div>
</div>

<?php
include '../includes/footer.php';
?>