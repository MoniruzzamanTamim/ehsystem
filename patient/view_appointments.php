<?php
session_start();
include __DIR__ . '/../config/dbconnect.php';

if (!isset($_SESSION['nid'])) { header("Location: login.php"); exit(); }
$patient_nid = $_SESSION['nid'];

// ১. অ্যাপয়েন্টমেন্ট ক্যানসেল করার লজিক
if (isset($_POST['cancel_appointment'])) {
    $appointment_id = mysqli_real_escape_string($conn, $_POST['appointment_id']);
    $cancel_note = mysqli_real_escape_string($conn, $_POST['cancel_note']);
    
    $cancel_query = "UPDATE appointments SET Status = 'Cancelled', Cancel_Note = '$cancel_note' WHERE id = '$appointment_id' AND Patient_NID = '$patient_nid'";
    
    if (mysqli_query($conn, $cancel_query)) {
        $_SESSION['success_flash'] = "Appointment cancelled successfully!";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $error_msg = "Error cancelling appointment: " . mysqli_error($conn);
    }
}

// ফ্ল্যাশ মেসেজ হ্যান্ডলিং
if (isset($_SESSION['success_flash'])) {
    $msg = $_SESSION['success_flash'];
    unset($_SESSION['success_flash']);
}

// ২. রানিং একটিভ শিডিউল কুয়েরি
$active_query = "SELECT a.*, d.Full_Name, 
                        t.Ticket_No, t.Status AS test_status, t.Report_File, t.report_delivery_time,
                        p.id AS prescription_id
                 FROM appointments a 
                 JOIN doctor d ON a.Doctor_NID = d.NID 
                 LEFT JOIN pathology_tickets t ON a.id = t.Appointment_Id
                 LEFT JOIN prescriptions p ON a.id = p.Appointment_Id
                 WHERE a.Patient_NID = '$patient_nid' AND a.Status IN ('Pending', 'Accepted', 'Finished', 'Completed')";

$active_res = mysqli_query($conn, $active_query);

// ৩. হিস্ট্রি ফিল্টারিং লজিক
$where_clauses = ["a.Patient_NID = '$patient_nid'", "a.Status IN ('Cancelled', 'Rejected')"];
if(!empty($_GET['filter_date'])) {
    $where_clauses[] = "DATE(a.Appointment_Time) = '".mysqli_real_escape_string($conn, $_GET['filter_date'])."'";
}
$where_str = implode(' AND ', $where_clauses);
$history_res = mysqli_query($conn, "SELECT a.*, d.Full_Name FROM appointments a JOIN doctor d ON a.Doctor_NID = d.NID WHERE $where_str ORDER BY a.id DESC");

$header_path = __DIR__ . '/includes/patient_header.php';
if (!file_exists($header_path)) {
    $header_path = __DIR__ . '/../includes/patient_header.php';
}
include $header_path;
?>

<div class="container-fluid px-4 mt-4">
    
    <?php if(isset($msg)) echo "<div class='alert alert-success alert-dismissible fade show' role='alert'><i class='fa-solid fa-circle-check me-2'></i>$msg<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>"; ?>
    <?php if(isset($error_msg)) echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'><i class='fa-solid fa-triangle-exclamation me-2'></i>$error_msg<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>"; ?>

    <div class="card mb-4 shadow-sm border-start border-primary border-4">
        <div class="card-header bg-white fw-bold text-primary"><i class="fa-solid fa-bell me-2"></i>Live Appointment Tracking</div>
        <div class="card-body table-responsive">
            <table class="table align-middle table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Appointment ID</th>
                        <th>Doctor Name</th>
                        <th>Appointment Status</th>
                        <th>Your Serial</th>
                        <th>Expected Time</th>
                        <th>Medical Actions / Updates</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($active_res) == 0) echo "<tr><td colspan='6' class='text-center text-muted py-3'>No active appointment at this moment.</td></tr>";
                    while($row = mysqli_fetch_assoc($active_res)) { ?>
                    <tr>
                        <td><span class="badge bg-secondary fs-6">#<?php echo $row['id']; ?></span></td>
                        <td><strong><?php echo htmlspecialchars($row['Full_Name']); ?></strong></td>
                        <td>
                            <span class="badge <?php 
                                if($row['Status'] == 'Accepted') echo 'bg-success';
                                elseif($row['Status'] == 'Finished') echo 'bg-info text-dark';
                                elseif($row['Status'] == 'Completed') echo 'bg-primary';
                                else echo 'bg-warning text-dark';
                            ?>">
                                <?php 
                                    if($row['Status'] == 'Accepted') echo 'Approved & Scheduled';
                                    elseif($row['Status'] == 'Finished') echo 'Prescribed / Lab Done';
                                    elseif($row['Status'] == 'Completed') echo 'Under Test Investigation';
                                    else echo 'Pending Review';
                                ?>
                            </span>
                        </td>
                        <td><span class="text-primary fw-bold fs-5"><?php echo $row['Serial_No'] ?? '---'; ?></span></td>
                        <td><i class="fa-regular fa-clock me-1 text-muted"></i> <?php echo $row['Appointment_Time'] ? date('h:i A (d-M)', strtotime($row['Appointment_Time'])) : 'Waiting for approval'; ?></td>
                        <td>
                            <div class="d-flex flex-column gap-2" style="max-width: 250px;">
                            <?php 
                            // 🔥 আপনার দেওয়া ডক্টর পোর্টালের লজিক অনুযায়ী ল্যাব টেস্টের লাইভ ট্র্যাকিং
                            if (!empty($row['test_status'])) {
                                $ticket_status = $row['test_status'];

                                if ($ticket_status == 'Pending') {
                                    echo '<span class="badge bg-warning text-dark p-2 text-start w-100"><i class="fa-solid fa-paper-plane me-1"></i> Sent to MT</span>';
                                } 
                                elseif ($ticket_status == 'Approved') {
                                    echo '<span class="badge bg-primary p-2 text-start w-100"><i class="fa-solid fa-thumbs-up me-1"></i> Approved</span>';
                                    if (!empty($row['Appointment_Time'])) {
                                        echo '<small class="d-block text-muted mt-1"><i class="fa-regular fa-clock me-1"></i>Scheduled: '.date('h:i A', strtotime($row['Appointment_Time'])).'</small>';
                                    }
                                } 
                                elseif ($ticket_status == 'Sample Collected' || $ticket_status == 'Collected') {
                                    echo '<span class="badge bg-info text-dark p-2 text-start w-100"><i class="fa-solid fa-vial me-1"></i> Sample Collected</span>';
                                    if (!empty($row['report_delivery_time'])) {
                                        echo '<small class="d-block text-muted mt-1"><i class="fa-regular fa-clock me-1"></i>Delivery: '.date('h:i A', strtotime($row['report_delivery_time'])).'</small>';
                                    }
                                } 
                                // ল্যাব স্ট্যাটাস Completed হলে বাটন শো করবে
                                elseif ($ticket_status == 'Completed') {
                                    if (!empty($row['Report_File'])) {
                                        echo '<a href="../uploads/reports/'.htmlspecialchars($row['Report_File']).'" target="_blank" class="btn btn-sm btn-success fw-bold w-100 mb-1"><i class="fa-solid fa-eye me-1"></i> View Lab Report</a>';
                                    } else {
                                        echo '<a href="../mt/view_report.php?ticket='.$row['Ticket_No'].'" target="_blank" class="btn btn-sm btn-success fw-bold w-100 mb-1"><i class="fa-solid fa-eye me-1"></i> View Report</a>';
                                    }

                                    // যদি ডক্টর এখনো প্রেসক্রিপশন তৈরি না করে থাকেন
                                    if (empty($row['prescription_id'])) {
                                        echo '<small class="text-danger text-center d-block fw-semibold mt-1"><i>Waiting for Doctor\'s final prescription...</i></small>';
                                    }
                                }
                            }

                            // ২. প্রেসক্রিপশন বাটন জেনারেট করা
                            if (!empty($row['prescription_id']) || $row['Status'] == 'Finished') {
                                echo '<a href="view_prescription.php?id='.$row['id'].'" class="btn btn-sm btn-primary w-100 fw-bold"><i class="fa-solid fa-prescription me-1"></i> View Prescription</a>';
                            }

                            // ৩. শুধুমাত্র রিভিউ পেন্ডিং অবস্থায় ক্যানসেল করার বাটন
                            if (empty($row['test_status']) && empty($row['prescription_id']) && $row['Status'] == 'Pending') { ?>
                                <button type="button" class="btn btn-sm btn-outline-danger cancel-btn w-100" data-bs-toggle="toggle" data-bs-target="#cancelModal" data-id="<?php echo $row['id']; ?>" onclick="openCancelModal(<?php echo $row['id']; ?>)">
                                    <i class="fa-solid fa-xmark me-1"></i> Cancel Appointment
                                </button>
                            <?php } ?>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow-sm mt-5 mb-5">
        <div class="card-header bg-secondary text-white fw-bold"><i class="fa-solid fa-folder-open me-2"></i>My Medical Appointment History</div>
        <div class="card-body">
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="date" name="filter_date" class="form-control form-control-sm" value="<?php echo isset($_GET['filter_date']) ? htmlspecialchars($_GET['filter_date']) : ''; ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-dark w-100"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
                </div>
            </form>
            
            <table class="table table-striped table-sm align-middle">
                <thead>
                    <tr>
                        <th>Appointment ID</th>
                        <th>Date</th>
                        <th>Doctor</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Cancel/Reject Note</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($history_res) == 0) echo '<tr><td colspan="6" class="text-muted text-center py-3">No past history found.</td></tr>';
                    while($row = mysqli_fetch_assoc($history_res)) { ?>
                    <tr>
                        <td><span class="badge bg-light text-dark border">#<?php echo $row['id']; ?></span></td>
                        <td><?php echo $row['Appointment_Time'] ? date('d M Y', strtotime($row['Appointment_Time'])) : 'N/A'; ?></td>
                        <td><strong><?php echo htmlspecialchars($row['Full_Name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['Reason']); ?></td>
                        <td>
                            <span class="badge <?php echo ($row['Status']=='Cancelled' ? 'bg-secondary' : 'bg-danger'); ?>">
                                <?php echo $row['Status']; ?>
                            </span>
                        </td>
                        <td class="text-danger fw-semibold small">
                            <?php echo !empty($row['Cancel_Note']) ? htmlspecialchars($row['Cancel_Note']) : '<span class="text-muted fw-normal">---</span>'; ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="cancelModalLabel"><i class="fa-solid fa-triangle-exclamation me-2"></i>Cancel Appointment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="appointment_id" id="modal_appointment_id">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Reason for Cancellation (Note)</label>
                        <textarea name="cancel_note" class="form-control" rows="3" placeholder="Please state the reason why you want to cancel this appointment..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn border" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="cancel_appointment" class="btn btn-danger">Confirm Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openCancelModal(id) {
    document.getElementById('modal_appointment_id').value = id;
    var myModal = new bootstrap.Modal(document.getElementById('cancelModal'));
    myModal.show();
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php include __DIR__ . '/../includes/footer.php'; ?>