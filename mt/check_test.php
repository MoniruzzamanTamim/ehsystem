<?php
session_start();
include '../config/dbconnect.php';

include 'admin_session_fix.php';

$mt_id = $_SESSION['mt'];

if (isset($_POST['update_status'])) {
    // ticket_no সিকিউর করা
    $ticket_no = mysqli_real_escape_string($conn, $_POST['ticket_no']);
    $current_action = $_POST['update_status'];

    if ($current_action == 'approve') {
        $sample_time = mysqli_real_escape_string($conn, $_POST['sample_time']);
        
        // কুয়েরিতে সব ছোট হাতের কলামের নাম ব্যবহার করা হয়েছে
        $sql = "UPDATE pathology_tickets SET 
                status='Approved', 
                collection_time='$sample_time', 
                mt_nid='$mt_id',
                notes=CONCAT(IFNULL(notes,''), '\n[MT Accepted | Collection Time: $sample_time]') 
                WHERE ticket_no='$ticket_no'";
                
        if(mysqli_query($conn, $sql)) {
            echo "<script>alert('Ticket Accepted Successfully'); window.location='check_test.php';</script>";
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } elseif ($current_action == 'cancel') {
        $sql = "UPDATE pathology_tickets SET 
                status='Cancelled', 
                notes=CONCAT(IFNULL(notes,''), '\n[MT Cancelled Request]') 
                WHERE ticket_no='$ticket_no'";
        
        if(mysqli_query($conn, $sql)) {
            echo "<script>alert('Request Cancelled'); window.location='check_test.php';</script>";
            exit();
        }
    }
}

// কুয়েরি: Pending টিকিটগুলো লোড করা
$query = "SELECT t.*, p.Full_Name 
          FROM pathology_tickets t 
          JOIN patient p ON t.patient_nid = p.NID 
          WHERE t.status='Pending' 
          ORDER BY t.id DESC";
$tickets_result = mysqli_query($conn, $query);

include '../includes/mt_header.php';
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header text-white bg-dark"><h4>New Incoming Test Requests</h4></div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Ticket No</th>
                        <th>Patient Info</th>
                        <th>Tests</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if (!$tickets_result || mysqli_num_rows($tickets_result) == 0) {
                    echo "<tr><td colspan='5' class='text-center'>No Pending Tickets Found</td></tr>";
                } else {
                    while ($row = mysqli_fetch_assoc($tickets_result)) {
                ?>
                <tr>
                    <td><?php echo htmlspecialchars((string)($row['ticket_no'] ?? '')); ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars((string)($row['Full_Name'] ?? '')); ?></strong><br>
                        NID: <?php echo htmlspecialchars((string)($row['patient_nid'] ?? '')); ?>
                    </td>
                    <td><?php echo nl2br(htmlspecialchars((string)($row['recommended_tests'] ?? ''))); ?></td>
                    <td><?php echo htmlspecialchars((string)($row['status'] ?? '')); ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="ticket_no" value="<?php echo htmlspecialchars((string)($row['ticket_no'] ?? '')); ?>">
                            <input type="datetime-local" name="sample_time" class="form-control mb-2" required>
                            <button type="submit" name="update_status" value="approve" class="btn btn-success btn-sm w-100 mb-1">Accept</button>
                            <button type="submit" name="update_status" value="cancel" class="btn btn-danger btn-sm w-100" onclick="return confirm('Are you sure?')">Cancel</button>
                        </form>
                    </td>
                </tr>
                <?php } } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>