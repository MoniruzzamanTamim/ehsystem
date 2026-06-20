<?php
session_start();
include '../config/dbconnect.php';
if (!isset($_SESSION['doctor'])) { header("Location: login.php"); exit(); }
$doctor_nid = $_SESSION['doctor'];

// ফিল্টারিং লজিক বিল্ড আপ
$where_clauses = ["a.Doctor_NID = '$doctor_nid'", "a.Status IN ('Completed', 'Cancelled')"];

if(!empty($_GET['filter_date'])) {
    $filter_date = $_GET['filter_date'];
    $where_clauses[] = "DATE(a.Appointment_Time) = '$filter_date'";
}
if(!empty($_GET['filter_status'])) {
    $filter_status = $_GET['filter_status'];
    $where_clauses[] = "a.Status = '$filter_status'";
}

$where_str = implode(' AND ', $where_clauses);
$query = "SELECT a.*, p.Full_Name FROM appointments a JOIN patient p ON a.Patient_NID = p.NID WHERE $where_str ORDER BY a.Appointment_Time DESC";
$history = mysqli_query($conn, $query);

include '../includes/doctor_header.php';
?>

<div class="card p-3 mb-4 bg-light">
    <form method="GET" class="row g-3 align-items-center">
        <div class="col-md-4">
            <label class="form-label fw-bold">Filter by Date</label>
            <input type="date" name="filter_date" class="form-control" value="<?php echo $_GET['filter_date'] ?? ''; ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label fw-bold">Filter by Status</label>
            <select name="filter_status" class="form-select">
                <option value="">All History</option>
                <option value="Completed" <?php if(($_GET['filter_status']??'')=='Completed') echo 'selected'; ?>>Completed</option>
                <option value="Cancelled" <?php if(($_GET['filter_status']??'')=='Cancelled') echo 'selected'; ?>>Cancelled</option>
            </select>
        </div>
        <div class="col-md-4 pt-4">
            <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-filter me-2"></i>Apply Filters</button>
            <a href="appointment_history.php" class="btn btn-secondary text-white">Reset</a>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header bg-dark text-white fw-bold"><i class="fa-solid fa-history me-2"></i>Doctor's Appointment History Log</div>
    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <thead class="table-secondary">
                <tr><th>Date & Time</th><th>Serial No</th><th>Patient Name</th><th>Reason</th><th>Final Status</th></tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($history) == 0) echo "<tr><td colspan='5' class='text-center text-muted'>No history found for this criteria.</td></tr>";
                while($row = mysqli_fetch_assoc($history)) { ?>
                <tr>
                    <td><?php echo $row['Appointment_Time']; ?></td>
                    <td><?php echo $row['Serial_No']; ?></td>
                    <td><?php echo $row['Full_Name']; ?></td>
                    <td><?php echo $row['Reason']; ?></td>
                    <td>
                        <span class="badge <?php echo ($row['Status']=='Completed') ? 'bg-success' : 'bg-danger'; ?>">
                            <?php echo $row['Status']; ?>
                        </span>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>