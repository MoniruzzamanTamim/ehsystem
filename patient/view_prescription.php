<?php
session_start();
if (!isset($_SESSION['nid'])) {
    header("Location: ../login.php");
    exit();
}

include '../config/dbconnect.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<div class='container mt-5'><h3>Invalid Request!</h3><a href='medical_history.php'>Go Back</a></div>";
    exit();
}

$id = (int)$_GET['id'];
$patient_nid = $_SESSION['nid'];

// আপনার পাঠানো কলাম নেম এবং স্ট্রাকচার অনুযায়ী SQL কুয়েরি আপডেট করা হয়েছে
$sql = "SELECT pr.`id`, pr.`Appointment_Id`, pr.`ticket_no`, pr.`patient_nid`, pr.`doctor_nid`, 
               pr.`doctor_name`, pr.`symptoms`, pr.`diagnosis`, pr.`medicines`, pr.`advice`, pr.`created_at`,
               p.Full_Name as Patient_Name, p.Phone as Patient_Phone, d.Specialization
        FROM `prescriptions` pr
        JOIN `patient` p ON pr.`patient_nid` = p.`NID`
        LEFT JOIN `doctor` d ON pr.`doctor_nid` = d.`NID`
        WHERE pr.`id` = ? AND pr.`patient_nid` = ?";

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "is", $id, $patient_nid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $p = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

if (!$p) {
    echo "<div class='container mt-5'><h3>Prescription not found or Access Denied!</h3><a href='medical_history.php'>Go Back</a></div>";
    exit();
}

include '../includes/patient_header.php';
?>

<div class="container mt-5 pad-container shadow p-5" style="background: #fff; border-radius: 8px;">
    
    <!-- হেডার: সরাসরি প্রিসক্রিপশন টেবিল থেকে doctor_name নেওয়া হচ্ছে -->
    <div class="text-center border-bottom mb-4 pb-3">
        <h3>Dr. <?php echo htmlspecialchars($p['doctor_name']); ?></h3>
        <?php if(!empty($p['Specialization'])): ?>
            <p><strong>Specialization:</strong> <?php echo htmlspecialchars($p['Specialization']); ?> | City Hospital</p>
        <?php else: ?>
            <p>City Hospital</p>
        <?php endif; ?>
        <!-- আপনার ডাটাবেজের created_at কলাম ব্যবহার করা হয়েছে -->
        <small class="text-muted">Date & Time: <?php echo htmlspecialchars($p['created_at']); ?></small>
    </div>
    
    <div class="row mb-4 p-2 bg-light rounded" style="font-size: 15px;">
        <div class="col-md-4">
            <strong>Patient Name:</strong> <?php echo htmlspecialchars($p['Patient_Name']); ?>
        </div>
        <div class="col-md-4 text-md-center">
            <strong>NID:</strong> <?php echo htmlspecialchars($p['patient_nid']); ?>
        </div>
        <div class="col-md-4 text-md-end">
            <strong>Contact:</strong> <?php echo htmlspecialchars($p['Patient_Phone'] ?? 'N/A'); ?>
        </div>
    </div>
    <hr>
    
    <div class="row">
        <!-- বাম পাশ: সিম্পটম ও ডায়াগনোসিস -->
        <div class="col-md-4 border-end">
            <div class="mb-4">
                <h6 class="text-muted"><i class="fa-solid fa-notes-medical"></i> Symptoms:</h6>
                <p><?php echo nl2br(htmlspecialchars($p['symptoms'])); ?></p>
            </div>
            <div class="mb-4">
                <h6 class="text-muted"><i class="fa-solid fa-stethoscope"></i> Diagnosis:</h6>
                <p><?php echo nl2br(htmlspecialchars($p['diagnosis'])); ?></p>
            </div>
        </div>

        <!-- ডান পাশ: মেডিসিন ও ডাক্তারের উপদেশ (Rx) -->
        <div class="col-md-8 ps-4">
            <div class="mb-4">
                <h5 style="color: #2c3e50;"><strong style="font-size: 24px;">R<sub>x</sub></strong> (Medicines):</h5>
                <div class="p-3 rounded" style="background: #fdfefe; border-left: 5px solid #99CC00; font-size: 16px; line-height: 1.6;">
                    <?php echo nl2br(htmlspecialchars($p['medicines'])); ?>
                </div>
            </div>

            <div class="mb-4">
                <h6 class="text-muted"><i class="fa-solid fa-comment-medical"></i> Advice / Instructions:</h6>
                <p class="p-2 bg-light rounded"><?php echo nl2br(htmlspecialchars($p['advice'] ?? 'No extra advice given.')); ?></p>
            </div>
        </div>
    </div>
    
    <div class="mt-5 pt-3 border-top no-print">
        <button class="btn btn-success me-2" onclick="window.print()"><i class="fa-solid fa-print"></i> Print Prescription</button>
        <a href="medical_history.php" class="btn btn-secondary">Back to List</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>