<?php
session_start();
include __DIR__ . '/../config/dbconnect.php';

if (!isset($_SESSION['nid'])) { header("Location: login.php"); exit(); }
$patient_nid = $_SESSION['nid'];

// ফ্ল্যাশ মেসেজ হ্যান্ডলিং
if (isset($_SESSION['success_flash'])) {
    $msg = $_SESSION['success_flash'];
    unset($_SESSION['success_flash']);
}

// হিস্ট্রি ফিল্টারিং লজিক (Completed, Finished, Cancelled, Rejected সব একসাথে আসবে)
$where_clauses = ["a.Patient_NID = '$patient_nid'", "a.Status IN ('Cancelled', 'Rejected', 'Finished', 'Completed')"];
if(!empty($_GET['filter_date'])) {
    $where_clauses[] = "DATE(a.Appointment_Time) = '".mysqli_real_escape_string($conn, $_GET['filter_date'])."'";
}
$where_str = implode(' AND ', $where_clauses);

// কুয়েরিতে ল্যাব রিপোর্ট এবং প্রেসক্রিপশন টেবিল জয়েন করা হয়েছে বাটন জেনারেট করার জন্য
$history_query = "SELECT a.*, d.Full_Name, 
                         t.Ticket_No, t.Status AS test_status, t.Report_File,
                         p.id AS prescription_id
                  FROM appointments a 
                  JOIN doctor d ON a.Doctor_NID = d.NID 
                  LEFT JOIN pathology_tickets t ON a.id = t.Appointment_Id
                  LEFT JOIN prescriptions p ON a.id = p.Appointment_Id
                  WHERE $where_str 
                  ORDER BY a.id DESC";

$history_res = mysqli_query($conn, $history_query);

$header_path = __DIR__ . '/includes/patient_header.php';
if (!file_exists($header_path)) {
    $header_path = __DIR__ . '/../includes/patient_header.php';
}
include $header_path;
?>

<div class="container-fluid px-4 mt-4">
    
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-secondary text-white fw-bold">
            <i class="fa-solid fa-folder-open me-2"></i>My Medical Appointment History
        </div>
        <div class="card-body">
            
            <!-- ফিল্টার ফর্ম -->
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="date" name="filter_date" class="form-control form-control-sm" value="<?php echo isset($_GET['filter_date']) ? htmlspecialchars($_GET['filter_date']) : ''; ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-dark w-100"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
                </div>
            </form>
            
            <!-- হিস্ট্রি টেবিল -->
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Appointment ID</th>
                            <th>Date</th>
                            <th>Doctor</th>
                            <th>Reason / Problem</th>
                            <th>Status</th>
                            <th>Medical Records (Report / Prescription)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($history_res) == 0) echo '<tr><td colspan="6" class="text-muted text-center py-3">No past history found.</td></tr>';
                        while($row = mysqli_fetch_assoc($history_res)) { ?>
                        <tr>
                            <td><span class="badge bg-light text-dark border">#<?php echo $row['id']; ?></span></td>
                            <td><?php echo $row['Appointment_Time'] ? date('d M Y', strtotime($row['Appointment_Time'])) : 'N/A'; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['Full_Name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['Reason'] ?? 'N/A'); ?></td>
                            <td>
                                <span class="badge <?php 
                                    if($row['Status'] == 'Completed') echo 'bg-primary';
                                    elseif($row['Status'] == 'Finished') echo 'bg-info text-dark';
                                    elseif($row['Status'] == 'Cancelled') echo 'bg-secondary';
                                    else echo 'bg-danger'; 
                                ?>">
                                    <?php echo htmlspecialchars($row['Status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                <?php 
                                // ১. যদি স্ট্যাটাস Completed বা Finished হয় এবং ল্যাব রিপোর্ট থাকে
                                if ($row['Status'] == 'Completed' || !empty($row['test_status'])) {
                                    if (!empty($row['Report_File'])) {
                                        echo '<a href="../uploads/reports/'.htmlspecialchars($row['Report_File']).'" target="_blank" class="btn btn-xs btn-success btn-sm fw-bold"><i class="fa-solid fa-file-medical me-1"></i> View Lab Report</a>';
                                    } elseif (!empty($row['Ticket_No'])) {
                                        echo '<a href="../mt/view_report.php?ticket='.$row['Ticket_No'].'" target="_blank" class="btn btn-xs btn-success btn-sm fw-bold"><i class="fa-solid fa-eye me-1"></i> View Report</a>';
                                    }
                                }

                                // ২. যদি প্রেসক্রিপশন জেনারেট হয়ে থাকে (prescription_id থাকে অথবা Status 'Finished' হয়)
                                if (!empty($row['prescription_id']) || $row['Status'] == 'Finished') {
                                    echo '<a href="view_prescription.php?id='.$row['id'].'" class="btn btn-xs btn-primary btn-sm fw-bold"><i class="fa-solid fa-prescription me-1"></i> View Prescription</a>';
                                }

                                // ৩. ক্যানসেল বা রিজেক্টেড হলে নোট দেখাবে
                                if ($row['Status'] == 'Cancelled' || $row['Status'] == 'Rejected') {
                                    echo !empty($row['Cancel_Note']) ? '<small class="text-danger fw-semibold"><b>Note:</b> '.htmlspecialchars($row['Cancel_Note']).'</small>' : '<span class="text-muted">---</span>';
                                }
                                ?>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php include __DIR__ . '/../includes/footer.php'; ?>