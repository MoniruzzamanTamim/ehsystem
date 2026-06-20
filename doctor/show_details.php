<?php
session_start();
include '../config/dbconnect.php';

// id চেক করা
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<div class='container mt-5'><h3>Invalid Request!</h3><a href='view_prescription.php'>Go Back</a></div>";
    exit();
}

$id = (int)$_GET['id'];
$query_pres = mysqli_query($conn, "SELECT * FROM prescriptions WHERE id = $id");
$p = mysqli_fetch_assoc($query_pres);

if (!$p) {
    echo "<div class='container mt-5'><h3>Prescription not found!</h3><a href='view_prescription.php'>Go Back</a></div>";
    exit();
}

// রোগীর তথ্য আনা
$pat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT Full_Name FROM patient WHERE NID = '{$p['patient_nid']}'"));
$pat_name = $pat['Full_Name'] ?? 'Unknown Patient';

include '../includes/header_link.php';
?>

<div class="container mt-5 pad-container shadow p-5">
    <div class="text-center border-bottom mb-4">
        <h3>Dr. <?php echo $_SESSION['doctor_name'] ?? 'Doctor'; ?></h3>
        <p>MBBS, FCPS | City Hospital</p>
    </div>
    
    <p><strong>Patient:</strong> <?php echo $pat_name; ?> | <strong>NID:</strong> <?php echo $p['patient_nid']; ?></p>
    <hr>
    
    <h5>Symptoms:</h5><p><?php echo nl2br(htmlspecialchars($p['symptoms'])); ?></p>
    <h5>Diagnosis:</h5><p><?php echo nl2br(htmlspecialchars($p['diagnosis'])); ?></p>
    <h5>Medicines:</h5><p><?php echo nl2br(htmlspecialchars($p['medicines'])); ?></p>
    <h5>Advice:</h5><p><?php echo nl2br(htmlspecialchars($p['advice'])); ?></p>
    
    <div class="mt-4 no-print">
        <button class="btn btn-primary" onclick="window.print()">Print</button>
        <a href="view_prescription.php" class="btn btn-secondary">Back</a>
    </div>
</div>
<?php include '../includes/footer.php'; ?>