<?php
session_start();
include '../config/dbconnect.php';
include 'admin_session_fix.php';

// সার্চ এবং ফিল্টার লজিক
$search = $_GET['nid_search'] ?? '';
$where = "WHERE t.status = 'Completed'"; // শুধুমাত্র যাদের রিপোর্ট রেডি হয়েছে

// Prepare and execute query safely to avoid SQL injection
if (!empty($search)) {
    $where .= " AND t.patient_nid LIKE ?";
    $stmt = mysqli_prepare($conn, "SELECT t.*, p.Full_Name FROM pathology_tickets t JOIN patient p ON t.patient_nid = p.NID $where ORDER BY t.id DESC");
    $like = '%' . $search . '%';
    mysqli_stmt_bind_param($stmt, 's', $like);
    mysqli_stmt_execute($stmt);
    $results = mysqli_stmt_get_result($stmt);
} else {
    $query = "SELECT t.*, p.Full_Name FROM pathology_tickets t JOIN patient p ON t.patient_nid = p.NID $where ORDER BY t.id DESC";
    $results = mysqli_query($conn, $query);
}

// Done বাটন হ্যান্ডলার (যদি রিপোর্ট ডেলিভারি কনফার্ম করতে চাও)
if(isset($_GET['mark_done'])) {
    // Use prepared statement for update
    $ticket = $_GET['mark_done'];
    $ustmt = mysqli_prepare($conn, "UPDATE pathology_tickets SET status='Delivered' WHERE ticket_no=?");
    mysqli_stmt_bind_param($ustmt, 's', $ticket);
    mysqli_stmt_execute($ustmt);
    echo "<script>alert('Report Marked as Delivered!'); window.location='delivered_report.php';</script>";
}

include '../includes/header_link.php';
?>

<div class="container mt-4">
    <h3>Delivered/Completed Reports</h3>
    
    <div class="card mb-3 p-3 shadow">
        <form method="GET" class="row g-2">
            <div class="col-md-10">
                <input type="text" name="nid_search" class="form-control" placeholder="Search by Patient NID..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">Search</button></div>
        </form>
    </div>

    <table class="table table-bordered bg-white shadow">
        <thead class="table-dark">
            <tr><th>Ticket No</th><th>Patient NID</th><th>Patient Name</th><th>Action</th></tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($results)) { ?>
            <tr>
                <td><?php echo $row['ticket_no']; ?></td>
                <td><?php echo $row['patient_nid']; ?></td>
                <td><?php echo $row['Full_Name']; ?></td>
                <td>
                    <a href="view_report.php?ticket=<?php echo $row['ticket_no']; ?>" class="btn btn-info btn-sm">View Report</a>
                    
                    <!-- <a href="?mark_done=<?php echo $row['ticket_no']; ?>" class="btn btn-success btn-sm" onclick="return confirm('Confirm Report Delivery?')">Done</a> -->
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php include '../includes/footer.php'; ?>