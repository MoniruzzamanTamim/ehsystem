<?php
session_start();
include '../config/dbconnect.php';
if (!isset($_SESSION['doctor'])) { header("Location: login.php"); exit(); }

// ফাইলের নিজস্ব নাম ডাইনামিকলি নেওয়ার জন্য
$current_page = basename($_SERVER['PHP_SELF']);

// Filter logic (WHERE ক্লজ জয়েন কুয়েরির সাথে মেলানো হয়েছে)
$where = "WHERE p.doctor_nid = '{$_SESSION['doctor']}'";
if (!empty($_GET['search'])) {
    $s = mysqli_real_escape_string($conn, $_GET['search']);
    $where .= " AND (p.ticket_no LIKE '%$s%' OR p.patient_nid LIKE '%$s%' OR p.Appointment_Id LIKE '%$s%')";
}
if (!empty($_GET['date'])) {
    $date = mysqli_real_escape_string($conn, $_GET['date']);
    $where .= " AND DATE(p.created_at) = '$date'";
}

// 🔥 appointments টেবিল JOIN করা হলো যাতে Appointment ID এবং Serial No শো করা যায়
$query = "SELECT p.*, a.Serial_No 
          FROM prescriptions p 
          LEFT JOIN appointments a ON p.Appointment_Id = a.id 
          $where 
          ORDER BY p.created_at DESC";

$result = mysqli_query($conn, $query);
include '../includes/header_link.php';
?>

<div class="container my-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div>
                    <h3 class="card-title mb-1"><i class="fa-solid fa-clock-history me-2 text-primary"></i>Prescription History</h3>
                    <p class="text-muted mb-0">Browse the latest prescriptions issued by your account.</p>
                </div>
                <span class="badge bg-secondary mt-3 mt-md-0"><?php echo mysqli_num_rows($result); ?> records</span>
            </div>
            
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-4">
                    <input type="text" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" class="form-control" placeholder="Search by Ticket, Appointment, or Patient ID">
                </div>
                <div class="col-md-3">
                    <input type="date" name="date" value="<?php echo isset($_GET['date']) ? htmlspecialchars($_GET['date']) : ''; ?>" class="form-control">
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter me-1"></i> Filter</button>
                </div>
                <div class="col-md-3 d-grid">
                    <a href="<?php echo $current_page; ?>" class="btn btn-outline-secondary"><i class="fa-solid fa-rotate-left me-1"></i> Reset / Back</a>
                </div>
            </form>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Appointment ID</th>
                            <th>Serial</th>
                            <th>Date</th>
                            <th>Ticket ID</th>
                            <th>Patient ID</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0) { while($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td>
                                <span class="badge bg-secondary">
                                    #<?php echo !empty($row['Appointment_Id']) ? htmlspecialchars($row['Appointment_Id']) : 'Direct'; ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-primary">
                                    <?php echo !empty($row['Serial_No']) ? htmlspecialchars($row['Serial_No']) : '---'; ?>
                                </span>
                            </td>
                            <td><?php echo date('d M Y, H:i', strtotime($row['created_at'])); ?></td>
                            <td>
                                <span class="badge <?php echo (!empty($row['ticket_no']) && $row['ticket_no'] !== 'N/A') ? 'bg-dark' : 'bg-light text-muted'; ?>">
                                    <?php echo htmlspecialchars($row['ticket_no']); ?>
                                </span>
                            </td>
                            <td><strong><?php echo htmlspecialchars($row['patient_nid']); ?></strong></td>
                            <td class="text-end">
                                <a href="show_details.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info me-2">
                                    <i class="fa-solid fa-eye me-1"></i> View Prescription
                                </a>
                                
                                <?php 
                                // কন্ডিশন: টিকিট যদি ফাঁকা না থাকে এবং N/A না হয়, তবেই শুধু View Report দেখাবে
                                if (!empty($row['ticket_no']) && $row['ticket_no'] !== 'N/A') { 
                                ?>
                                    <a href="../mt/view_report.php?ticket=<?php echo urlencode($row['ticket_no']); ?>" class="btn btn-sm btn-success">
                                        <i class="fa-solid fa-flask me-1"></i> View Report
                                    </a>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php } } else { ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No prescriptions found for the selected filters.</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>