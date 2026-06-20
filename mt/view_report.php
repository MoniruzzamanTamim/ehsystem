<?php
include '../config/dbconnect.php';
$ticket_no = $_GET['ticket'] ?? '';

// ডাটা ফেচ করা
$query = "SELECT t.*, p.Full_Name as Patient_Name, p.NID 
          FROM pathology_tickets t 
          JOIN patient p ON t.patient_nid = p.NID 
          WHERE t.ticket_no = '$ticket_no'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

if (!$row) { echo "Report not found!"; exit(); }

// JSON থেকে রিপোর্ট ডাটা ডিকোড করা
$report_items = json_decode($row['report_data'], true) ?? [];

include '../includes/header_link.php';
?>

<!-- Header  -->
 

<div class="container mt-5">
    <div class="card shadow p-4">
        <h3 class="text-center border-bottom pb-3">Laboratory Report: <?php echo $ticket_no; ?></h3>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <p><strong>Patient Name:</strong> <?php echo $row['Patient_Name']; ?></p>
                <p><strong>Patient NID:</strong> <?php echo $row['NID']; ?></p>
                <p><strong>Doctor ID:</strong> <?php echo $row['doctor_nid']; ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Ticket ID:</strong> <?php echo $ticket_no; ?></p>
                <p><strong>MT Name:</strong> <?php echo $row['Full_Name'] ?? 'N/A'; ?></p> 
                <p><strong>Reason/Note:</strong> <?php echo nl2br($row['recommended_tests']); ?></p>
            </div>
        </div>

        <h5 class="mt-4">Test Results:</h5>
        <?php foreach ($report_items as $index => $item): ?>
            <div class="card mb-3 p-3">
    <h6><?php echo ($index + 1) . ". " . htmlspecialchars($item['name']); ?></h6>
    
    <img src="../uploads/reports/<?php echo htmlspecialchars($item['file']); ?>" 
         class="img-fluid" 
         style="max-width: 400px;" 
         alt="Report Image">
</div>
        <?php endforeach; ?>

        <div class="mt-4">
            <h6>Comments / Final Report:</h6>
            <div class="bg-light p-3"><?php echo nl2br($row['report_text_note']); ?></div>
        </div>
        
        <div class="mt-4">
            <button class="btn btn-secondary me-2" onclick="history.back()">Back</button>
            <button class="btn btn-primary" onclick="window.print()">Print Report</button>
        </div>
    </div>
</div>