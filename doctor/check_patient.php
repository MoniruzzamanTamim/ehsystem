<?php
session_start();
include '../config/dbconnect.php';

if (!isset($_SESSION['doctor'])) { header("Location: login.php"); exit(); }
$doctor_nid = $_SESSION['doctor'];

$view_patient = false;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id'];
    
    $stmt = mysqli_prepare($conn, "SELECT a.*, p.Full_Name, p.NID, p.Phone, p.Address, p.Blood_Group FROM appointments a JOIN patient p ON a.Patient_NID = p.NID WHERE a.id = ? AND a.Doctor_NID = ?");    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'is', $id, $doctor_nid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        if ($row) {
            $view_patient = true;
            $patient_nid = $row['NID'];
            $patient_name = $row['Full_Name'];
            // ensure phone is available to avoid undefined variable notice
            $phone = isset($row['Phone']) ? $row['Phone'] : '';
            $address = isset($row['Address']) ? $row['Address'] : '';
            $blood_group = isset($row['Blood_Group']) ? $row['Blood_Group'] : '';
        }
    }
}

// ডক্টর ফর্ম সাবমিট করলে ব্যাকএন্ড প্রসেসিং
if (isset($_POST['submit_workflow'])) {
    $id = (int) $_POST['appointment_id'];
    $patient_nid = mysqli_real_escape_string($conn, $_POST['patient_nid']);
    $test_recommendations = mysqli_real_escape_string($conn, $_POST['test_recommendations']);
    
    // ১. ডাটাবেজ থেকে রোগীর লেটেস্ট তথ্য ফেচ করা
    $info_query = mysqli_query($conn, "SELECT Phone, Address FROM patient WHERE NID = '$patient_nid'");
    $info = mysqli_fetch_assoc($info_query);
    
    // যদি ডাটা না পাওয়া যায়, তবে 'N/A' সেট করা
    $contact = $info['Phone'] ?? 'N/A';
    $address = $info['Address'] ?? 'N/A';
    
    // ২. টিকিট জেনারেট করা
    if (!empty(trim($test_recommendations))) {
        $ticket_no = "LAB-" . date('Y') . "-" . rand(1000, 9999);
        
        // 🔥 ফিক্সড: এখানে ঠিক ৭টি '?' প্লেসহোল্ডার দেওয়া হয়েছে, ফলে টাইপ সংখ্যা (sssssssi) এখন হুবহু মিলবে
        $stmt2 = mysqli_prepare($conn, "INSERT INTO pathology_tickets 
            (ticket_no, patient_nid, doctor_nid, recommended_tests, status, patient_contact, patient_address, Appointment_Id) 
            VALUES (?, ?, ?, ?, 'Pending', ?, ?, ?)");
        
        // ssssss'i' মানে ৬টি স্ট্রিং এবং ১টি ইন্টিজার ভেরিয়েবল (মোট ৭টি ডেটা প্যারামিটার)
        mysqli_stmt_bind_param($stmt2, 'ssssssi', $ticket_no, $patient_nid, $doctor_nid, $test_recommendations, $contact, $address, $id);
        
        if(mysqli_stmt_execute($stmt2)) {
            // ডাটাবেজে সফলভাবে ইনসার্ট হয়েছে
        } else {
            echo "Error: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt2);
    }
    
    mysqli_query($conn, "UPDATE appointments SET Status='Completed' WHERE id='$id'");
    
    echo "<script>alert('Workflow completed and ticket created with contact info!'); window.location.href='check_patient.php';</script>";
    exit();
}

// টেবিল ১: সব Accepted রোগীর সিরিয়াল তালিকা (যারা ডক্টরের রুমের সামনে অপেক্ষমান)
$accepted_list = mysqli_query($conn, "SELECT a.*, p.Full_Name, p.Phone, p.Address FROM appointments a JOIN patient p ON a.Patient_NID = p.NID WHERE a.Doctor_NID = '$doctor_nid' AND a.Status = 'Accepted' ORDER BY a.Serial_No ASC");

// টেবিল ২: শুধুমাত্র যেসকল রোগীর ল্যাব টেস্ট পাঠানো হয়েছে, তাদের লাইভ ট্র্যাক কুয়েরি
$lab_filter_status = 'All';
$lab_filter_nid = '';
$valid_lab_statuses = ['All', 'Pending', 'Approved', 'Collected', 'Sample Collected', 'Completed'];
if (isset($_GET['lab_status']) && in_array($_GET['lab_status'], $valid_lab_statuses, true)) {
    $lab_filter_status = $_GET['lab_status'];
}
if (isset($_GET['patient_nid'])) {
    $lab_filter_nid = trim($_GET['patient_nid']);
}

$lab_status_condition = '';
if ($lab_filter_status !== 'All') {
    if ($lab_filter_status === 'Collected' || $lab_filter_status === 'Sample Collected') {
        $lab_status_condition = " AND (t.Status = 'Collected' OR t.Status = 'Sample Collected')";
    } else {
        $lab_status_condition = " AND t.Status = '" . mysqli_real_escape_string($conn, $lab_filter_status) . "'";
    }
}

$lab_nid_condition = '';
if ($lab_filter_nid !== '') {
    $lab_nid_condition = " AND a.Patient_NID = '" . mysqli_real_escape_string($conn, $lab_filter_nid) . "'";
}

$pending_report_list = mysqli_query($conn, "SELECT a.id as Appointment_ID, a.Serial_No, a.Patient_NID, p.Full_Name, p.Phone, p.Address, a.Reason, t.Status as Ticket_Status, t.Ticket_No FROM appointments a JOIN patient p ON a.Patient_NID = p.NID JOIN pathology_tickets t ON a.id = t.Appointment_Id WHERE a.Doctor_NID = '$doctor_nid' AND t.Doctor_NID = '$doctor_nid'" . $lab_status_condition . $lab_nid_condition . " ORDER BY t.id DESC");

include '../includes/doctor_header.php';
?>

<div class="container mt-4">

    <?php if ($view_patient): ?>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fa-solid fa-user-check me-2"></i>Patient Check-up</h4>
                        <a href="check_patient.php" class="btn btn-sm btn-light">Back to Queue</a>
                    </div>
                    <div class="card-body p-4">
                        <div class="row bg-light p-3 rounded mb-4">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Patient Name:</strong> <?php echo htmlspecialchars($patient_name); ?></p>
                                <p class="mb-0"><strong>Patient NID:</strong> <?php echo htmlspecialchars($patient_nid); ?></p>
                                <p class="mb-0"><strong>Patient Contact:</strong> <?php echo htmlspecialchars($phone); ?></p>
                                <p class="mb-0"><strong>Patient Address:</strong> <?php echo htmlspecialchars($address); ?></p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <p class="mb-1"><strong>Serial No:</strong> <span class="badge bg-dark fs-6"><?php echo $row['Serial_No']; ?></span></p>
                                <p class="mb-1"><strong>Blood Group:</strong> <span class="badge bg-dark fs-6"><?php echo htmlspecialchars($blood_group); ?></span></p>
                                <p class="mb-0 text-muted"><strong>Reason:</strong> <?php echo htmlspecialchars($row['Reason']); ?></p>
                            </div>
                        </div>

                        <div id="initialActions" class="d-flex justify-content-center gap-3 mb-2">
                            <a href="prescription.php?id=<?php echo $id; ?>" class="btn btn-outline-primary py-2 px-4">
                                <i class="fa-solid fa-file-medical me-1"></i> Create Prescription
                            </a>
                            <button type="button" class="btn btn-success py-2 px-4" onclick="showTreatmentForm()">
                                <i class="fa-solid fa-circle-check me-1"></i> Apply For Test
                            </button>
                        </div>

                        <form id="treatmentForm" method="POST" class="d-none mt-4">
                            <input type="hidden" name="appointment_id" value="<?php echo $id; ?>">
                            <input type="hidden" name="patient_nid" value="<?php echo $patient_nid; ?>">

                            <div class="alert alert-info py-2"><i class="fa-solid fa-info-circle me-1"></i> Filling up treatment workflow details:</div>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-secondary"><i class="fa-solid fa-pen-to-square me-1"></i> Step 1: Patient's Problems / Doctor Notes</label>
                                <textarea name="problem_note" class="form-control" rows="3" placeholder="Enter patient complaints... (Optional)"></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-secondary"><i class="fa-solid fa-flask text-danger me-1"></i> Step 2: Recommended Laboratory Tests (Line-by-Line)</label>
                                <textarea id="testArea" name="test_recommendations" class="form-control" rows="4" placeholder="1. "></textarea>
                                <div class="form-text text-muted">Press <strong>Enter</strong> to go to the next line. Each line represents a test.</div>
                            </div>

                            <hr>
                            
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="button" class="btn btn-secondary" onclick="hideTreatmentForm()">Cancel</button>
                                <button type="submit" name="submit_workflow" class="btn btn-success px-4 fw-bold">
                                    <i class="fa-solid fa-circle-check me-1"></i> Submit & Done
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <div class="card shadow mb-5">
            <div class="card-header bg-success text-white fw-bold">
                <h4 class="mb-0"><i class="fa-solid fa-hospital-user me-2"></i>Accepted Patients Queue (অপেক্ষমান তালিকা)</h4>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Appointment ID</th>
                            <th>Serial</th>
                            <th>Patient ID (NID)</th>
                            <th>Patient Name</th>
                            <th>Reason</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($accepted_list) == 0): ?>
                            <tr><td colspan="6" class='text-center text-muted py-3'>No accepted patients in the queue.</td></tr>
                        <?php else: ?>
                            <?php while($p_row = mysqli_fetch_assoc($accepted_list)) { ?>
                            <tr>
                                <td><span class="badge bg-secondary fs-6">#<?php echo $p_row['id']; ?></span></td>
                                <td><span class="badge bg-primary fs-6"><?php echo $p_row['Serial_No']; ?></span></td>
                                <td><?php echo htmlspecialchars($p_row['Patient_NID']); ?></td>
                                <td><strong><?php echo htmlspecialchars($p_row['Full_Name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($p_row['Reason']); ?></td>
                                <td>
                                    <a href="check_patient.php?id=<?php echo $p_row['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fa-solid fa-stethoscope"></i> Treat Patient
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header text-white fw-bold" style="background-color: #6610f2;">
                <h4 class="mb-0"><i class="fa-solid fa-hourglass-half me-2"></i>Lab Test & Report Tracking Queue</h4>
            </div>
            <div class="card-body table-responsive">
                <form method="get" class="row g-2 align-items-end mb-3">
                    <div class="col-auto">
                        <label for="labStatusFilter" class="form-label fw-semibold mb-0">Filter Status</label>
                        <select id="labStatusFilter" name="lab_status" class="form-select form-select-sm">
                            <option value="All"<?php echo $lab_filter_status === 'All' ? ' selected' : ''; ?>>All Statuses</option>
                            <option value="Pending"<?php echo $lab_filter_status === 'Pending' ? ' selected' : ''; ?>>Pending</option>
                            <option value="Approved"<?php echo $lab_filter_status === 'Approved' ? ' selected' : ''; ?>>Approved</option>
                            <option value="Sample Collected"<?php echo $lab_filter_status === 'Sample Collected' || $lab_filter_status === 'Collected' ? ' selected' : ''; ?>>Sample Collected</option>
                            <option value="Completed"<?php echo $lab_filter_status === 'Completed' ? ' selected' : ''; ?>>Completed</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <label for="patientNidFilter" class="form-label fw-semibold mb-0">Patient NID</label>
                        <input id="patientNidFilter" type="text" name="patient_nid" value="<?php echo htmlspecialchars($lab_filter_nid); ?>" class="form-control form-control-sm" placeholder="Enter NID">
                    </div>
                    <div class="col-auto">
                        <label for="ticketIdFilter" class="form-label fw-semibold mb-0">Ticket ID</label>
                        <input id="ticketIdFilter" type="text" name="ticket_id" value="<?php echo isset($_GET['ticket_id'])?htmlspecialchars($_GET['ticket_id']):''; ?>" class="form-control form-control-sm" placeholder="Enter Ticket ID">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-sm btn-primary">Apply Filter</button>
                        <?php $__ticket_val = isset($_GET['ticket_id'])?trim($_GET['ticket_id']):''; if ($lab_filter_status !== 'All' || $lab_filter_nid !== '' || $__ticket_val !== ''): ?>
                            <a href="check_patient.php" class="btn btn-sm btn-secondary">Reset</a>
                        <?php endif; ?>
                    </div>
                </form>
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-secondary">
                        <tr>
                            <th>Serial</th>
                            <th>Appointment ID</th>
                            <th>Patient INFO </th>
                            <th>Reason</th>
                            <th>Ticket ID</th>
                            <th>Lab Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($pending_report_list) == 0): ?>
                            <tr><td colspan="7" class='text-center text-muted py-3'>No patients have pending laboratory reports.</td></tr>
                        <?php else: ?>
                            <?php while($pr_row = mysqli_fetch_assoc($pending_report_list)) { 
                                $ticket_status = $pr_row['Ticket_Status'];
                                $badge_html = "";
                                
                                if($ticket_status == 'Pending') {
                                    $badge_html = '<span class="badge bg-warning text-dark"><i class="fa-solid fa-paper-plane me-1"></i> Sent to MT</span>';
                                } elseif($ticket_status == 'Approved') {
                                    $badge_html = '<span class="badge bg-primary"><i class="fa-solid fa-thumbs-up me-1"></i> Approved</span>';
                                } elseif($ticket_status == 'Sample Collected' || $ticket_status == 'Collected') {
                                    $badge_html = '<span class="badge bg-info text-dark"><i class="fa-solid fa-vial me-1"></i> Sample Collected</span>';
                                } elseif($ticket_status == 'Completed') {
                                    $badge_html = '<a href="../mt/view_report.php?ticket='.$pr_row['Ticket_No'].'" class="btn btn-sm btn-success fw-bold"><i class="fa-solid fa-eye me-1"></i> View Report</a> <a href="prescription.php?id='.$pr_row['Appointment_ID'].'" class="btn btn-sm btn-info fw-bold"><i class="fa-solid fa-prescription me-1"></i> Prescription</a>';
                                } else {
                                    $badge_html = '<span class="badge bg-secondary">Unknown</span>';
                                }
                            ?>
                            <tr>
                                <td><span class="badge bg-secondary fs-6"><?php echo $pr_row['Serial_No']; ?></span></td>
                                <td><span class="badge bg-light text-dark border">#<?php echo htmlspecialchars($pr_row['Appointment_ID']); ?></span></td>
                                <td>
                                    <small>Patient NID: <?php echo htmlspecialchars($pr_row['Patient_NID'] ?? 'N/A'); ?></small> <br>
                                    <strong>Patient Name: <?php echo htmlspecialchars($pr_row['Full_Name'] ?? 'N/A'); ?></strong> <br>
                                    <small>Contact: <?php echo htmlspecialchars($pr_row['address'] ?? 'N/A'); ?></small> <br>
                                    <small>Address: <?php echo htmlspecialchars($pr_row['Patient_Address'] ?? 'N/A'); ?></small>
                                </td>
                                 
                                <td></td>
                                <td><?php echo htmlspecialchars($pr_row['Reason']); ?></td>
                                <td><span class="badge bg-dark"><?php echo htmlspecialchars($pr_row['Ticket_No']); ?></span></td>
                                <td><?php echo $badge_html; ?></td>
                            </tr>
                            <?php } ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php endif; ?>

</div>

<script type="text/javascript">
function showTreatmentForm() {
    document.getElementById('treatmentForm').classList.remove('d-none');
    document.getElementById('initialActions').classList.add('d-none');
}

function hideTreatmentForm() {
    document.getElementById('treatmentForm').classList.add('d-none');
    document.getElementById('initialActions').classList.remove('d-none');
}

document.getElementById('testArea')?.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault(); 
        
        let cursorPos = this.selectionStart;
        let textBefore = this.value.substring(0, cursorPos);
        let textAfter  = this.value.substring(cursorPos);
        
        let lines = textBefore.split('\n');
        let nextLineNumber = lines.length + 1;
        
        this.value = textBefore + '\n' + nextLineNumber + '. ' + textAfter;
        this.selectionStart = this.selectionEnd = cursorPos + 1 + (nextLineNumber + '. ').length;
    }
});
</script>

<?php include '../includes/footer.php'; ?>