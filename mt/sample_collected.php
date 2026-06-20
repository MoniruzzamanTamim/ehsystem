<?php
session_start();
include '../config/dbconnect.php';
include 'admin_session_fix.php';

// সার্চ এবং ফিল্টার লজিক
$search = $_GET['nid_search'] ?? '';
$where = "WHERE t.status = 'Collected'";
if (!empty($search)) {
    $where .= " AND t.patient_nid LIKE '%$search%'";
}

$query = "SELECT t.*, p.Full_Name, p.Address FROM pathology_tickets t JOIN patient p ON t.patient_nid = p.NID $where ORDER BY t.id DESC";
$tickets_result = mysqli_query($conn, $query);

include '../includes/header_link.php';
?>

<div class="container mt-4">
    <div class="card mb-3 p-3 shadow">
        <form method="GET" class="row g-2">
            <div class="col-md-10"><input type="text" name="nid_search" class="form-control" placeholder="Search by NID..."></div>
            <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">Search</button></div>
        </form>
    </div>

    <table class="table table-bordered bg-white shadow">
        <thead class="table-dark">
            <tr>
                <th>Ticket No</th>
                <th>Patient ID</th>
                <th>Name</th>
                <th>Address</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($tickets_result)) { ?>
            <tr>
                <td><?php echo $row['ticket_no']; ?></td>
                <td><?php echo $row['patient_nid']; ?></td>
                <td><?php echo $row['Full_Name']; ?></td>
                <td><?php echo $row['Address']; ?></td>
                <td>
                    <a href="report_upload.php?ticket=<?php echo $row['ticket_no']; ?>" class="btn btn-warning btn-sm">Delivery Report</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php include '../includes/footer.php'; ?>