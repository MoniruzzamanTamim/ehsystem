<?php
session_start();
include '../config/dbconnect.php';

include 'admin_session_fix.php';

// স্যাম্পল কালেকশন সম্পন্ন বা বাতিল করার হ্যান্ডলিং
if (isset($_POST['action_type'])) {
    $ticket_no = mysqli_real_escape_string($conn, $_POST['ticket_no']);
    $action = $_POST['action_type'];

    if ($action == 'done_collect') {
        $delivery_time = mysqli_real_escape_string($conn, $_POST['delivery_time']);
        // কলামের নাম ছোট হাতের করা হয়েছে
        $sql = "UPDATE pathology_tickets SET 
                status='Collected', 
                report_delivery_time='$delivery_time',
                notes=CONCAT(IFNULL(notes,''), '\n[MT: Sample Collected | Delivery Expected: ', '$delivery_time', ']') 
                WHERE ticket_no='$ticket_no'";
        mysqli_query($conn, $sql);
        echo "<script>alert('Sample Collection Successful!'); window.location='sample_collected.php';</script>";
        exit();
    } elseif ($action == 'cancel_sample') {
        $cancel_note = mysqli_real_escape_string($conn, $_POST['cancel_note']);
        $sql = "UPDATE pathology_tickets SET 
                status='Cancelled', 
                notes=CONCAT(IFNULL(notes,''), '\n[MT: Cancelled | Reason: ', '$cancel_note', ']') 
                WHERE ticket_no='$ticket_no'";
        mysqli_query($conn, $sql);
        echo "<script>alert('Request Cancelled.'); window.location='sample_collected.php';</script>";
        exit();
    }
}

// কুয়েরি: ডাটাবেজের কলামের নাম অনুযায়ী কুয়েরি আপডেট করা হয়েছে
$query = "SELECT t.*, p.Full_Name 
          FROM pathology_tickets t 
          JOIN patient p ON t.patient_nid = p.nid 
          WHERE t.status = 'Approved' 
          ORDER BY t.collection_time ASC";
$tickets_result = mysqli_query($conn, $query);

include '../includes/mt_header.php';
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: #17a2b8;">
            <h4 class="mb-0"><i class="fa-solid fa-vials me-2"></i>Sample Collection Queue</h4>
            <span class="badge bg-light text-dark"><?php echo $tickets_result ? mysqli_num_rows($tickets_result) : 0; ?> Waiting</span>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Ticket No</th>
                        <th>Patient Info</th>
                        <th>Collection Time</th>
                        <th>Tests</th>
                        <th>Deliver Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($tickets_result)) { ?>
                    <tr>
                        <td><span class="badge bg-dark"><?php echo htmlspecialchars((string)($row['ticket_no'] ?? '')); ?></span></td>
        
                        <td>
                            <strong><?php echo htmlspecialchars((string)($row['Full_Name'] ?? '')); ?></strong><br>
                            <small>NID: <?php echo htmlspecialchars((string)($row['patient_nid'] ?? '')); ?></small><br>
                            <small>Contact: <?php echo htmlspecialchars((string)($row['patient_contact'] ?? '')); ?></small><br>
                            <small>Address: <?php echo htmlspecialchars((string)($row['patient_address'] ?? '')); ?></small>
                     </td>

                        <td class="text-primary fw-bold"><?php echo htmlspecialchars((string)($row['collection_time'] ?? '')); ?></td>

                        <td><?php echo nl2br(htmlspecialchars((string)($row['recommended_tests'] ?? ''))); ?></td>

                        <td>
                            <input type="datetime-local" name="delivery_time" form="actionForm_<?php echo $row['ticket_no']; ?>" class="form-control form-control-sm" required>
                        </td>

                        <td>
                         <form id="actionForm_<?php echo $row['ticket_no']; ?>" method="POST">
                                <input type="hidden" name="ticket_no" value="<?php echo htmlspecialchars((string)$row['ticket_no'] ?? ''); ?>">
                                <div class="d-flex flex-column gap-1">
                                    <button type="submit" name="action_type" value="done_collect" class="btn btn-success btn-sm">Collected</button>
                                 <button type="submit" name="action_type" value="cancel_sample" class="btn btn-danger btn-sm">Cancel</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>