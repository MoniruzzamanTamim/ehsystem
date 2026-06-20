<?php
session_start();
include '../config/dbconnect.php';
if (!isset($_SESSION['doctor'])) { header("Location: login.php"); exit(); }

$p = null;
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $p = mysqli_fetch_assoc(mysqli_query($conn, "SELECT a.*, p.Full_Name, p.Phone, p.Address, p.NID FROM appointments a JOIN patient p ON a.Patient_NID = p.NID WHERE a.id = $id"));
    if ($p) {
        $ticket_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT ticket_no FROM pathology_tickets WHERE patient_nid = '".mysqli_real_escape_string($conn, $p['NID'])."' ORDER BY id DESC LIMIT 1"));
        if ($ticket_row && !empty($ticket_row['ticket_no'])) {
            $p['ticket_no'] = $ticket_row['ticket_no'];
        }
    }
} elseif (isset($_GET['ticket'])) {
    $ticket = mysqli_real_escape_string($conn, $_GET['ticket']);
    $p = mysqli_fetch_assoc(mysqli_query($conn, "SELECT t.*, p.Full_Name, p.Phone, p.Address, p.NID FROM pathology_tickets t JOIN patient p ON t.patient_nid = p.NID WHERE t.ticket_no = '$ticket'"));
    $p['Reason'] = $p['recommended_tests'] ?? 'N/A';
}

if (!$p) {
    echo "<script>alert('Patient or appointment not found.'); window.location='check_patient.php';</script>";
    exit();
}

if(isset($_POST['save_prescription'])) {
    $nid = mysqli_real_escape_string($conn, $p['NID']);
    $ticket_no = mysqli_real_escape_string($conn, $p['ticket_no'] ?? 'N/A');
    $appointment_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
    $symptoms = mysqli_real_escape_string($conn, $_POST['symptoms']);
    $diagnosis = mysqli_real_escape_string($conn, $_POST['diagnosis']);
    $medicines = mysqli_real_escape_string($conn, $_POST['medicines']);
    $advice = mysqli_real_escape_string($conn, $_POST['advice']);
    
    $appointment_id_sql = $appointment_id !== null ? "'$appointment_id'" : 'NULL';
    $sql = "INSERT INTO prescriptions (ticket_no, patient_nid, doctor_nid, appointment_id, symptoms, diagnosis, medicines, advice) 
            VALUES ('$ticket_no', '$nid', '{$_SESSION['doctor']}', $appointment_id_sql, '$symptoms', '$diagnosis', '$medicines', '$advice')";
    
    if(mysqli_query($conn, $sql)) {
        if ($appointment_id !== null) {
            mysqli_query($conn, "UPDATE appointments SET Status='Finished' WHERE id='$appointment_id'");
        }
        echo "<script>alert('Prescription Saved!'); window.location='done_patient_list.php';</script>";
        exit();
    }
}

include '../includes/header_link.php';
?>

<div class="container mt-5">
    <div class="card shadow p-4">
        <h3 class="text-center mb-4">Patient Prescription & Records</h3>
        <div class="row">
            <div class="col-md-6">
                <p><strong>Patient Name:</strong> <?php echo $p['Full_Name'] ?? 'N/A'; ?></p>
                <p><strong>Patient ID (NID):</strong> <?php echo $p['NID'] ?? 'N/A'; ?></p>
                <p><strong>Contact:</strong> <?php echo $p['Phone'] ?? 'N/A'; ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Address:</strong> <?php echo $p['Address'] ?? 'N/A'; ?></p>
                <p><strong>Appointment ID:</strong> <?php echo $_GET['id'] ?? 'N/A'; ?></p>
                <p><strong>Ticket ID:</strong> <?php echo $p['ticket_no'] ?? 'N/A'; ?></p>
                <p><strong>Problem/Reason:</strong> <?php echo $p['Reason'] ?? 'N/A'; ?></p>
            </div>
            <div class="col-md-12">
               
            </div>
        </div>
        <hr>
        <?php if (isset($p['ticket_no'])): ?>
            <a href="../mt/view_report.php?ticket=<?php echo $p['ticket_no']; ?>" class="btn btn-success mb-3"><i class="fa-solid fa-eye"></i> View Lab Report</a>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3"><label><strong>Symptoms:</strong></label><input type="text" name="symptoms" class="form-control" required></div>
            <div class="mb-3"><label><strong>Diagnosis:</strong></label><textarea name="diagnosis" class="form-control" rows="2" required></textarea></div>
            <div class="mb-3"><label><strong>Medicines:</strong></label><textarea name="medicines" class="form-control" rows="5" placeholder="Rx..." required></textarea></div>
            <div class="mb-3"><label><strong>Advice:</strong></label><textarea name="advice" class="form-control" rows="2"></textarea></div>
            <button type="submit" name="save_prescription" class="btn btn-success">Save Prescription</button>
            <a href="check_patient.php" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>
<?php include '../includes/footer.php'; ?>