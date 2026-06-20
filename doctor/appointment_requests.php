<?php
session_start();
include __DIR__ . '/../config/dbconnect.php';
if (!isset($_SESSION['doctor'])) { header("Location: login.php"); exit(); }
$doctor_nid = $_SESSION['doctor'];

$success_msg = "";

// ১. অ্যাকশন হ্যান্ডলার (Accept / Done / Cancel)
if (isset($_POST['action'])) {
    $id = mysqli_real_escape_string($conn, $_POST['appointment_id']);
    
    if ($_POST['action'] == 'accept') {
        $serial = mysqli_real_escape_string($conn, $_POST['serial_no']);
        $time = mysqli_real_escape_string($conn, $_POST['app_time']);
        mysqli_query($conn, "UPDATE appointments SET Status='Accepted', Serial_No='$serial', Appointment_Time='$time' WHERE id='$id'");
        $success_msg = "Appointment accepted successfully!";
        
    } elseif ($_POST['action'] == 'cancel') {
        mysqli_query($conn, "UPDATE appointments SET Status='Cancelled' WHERE id='$id'");
        $success_msg = "Appointment cancelled successfully!";
    }
}

// ২. ডাটা কুয়েরি
$pending = mysqli_query($conn, "SELECT a.*, p.Full_Name FROM appointments a JOIN patient p ON a.Patient_NID = p.NID WHERE a.Doctor_NID = '$doctor_nid' AND a.Status = 'Pending'");
$accepted = mysqli_query($conn, "SELECT a.*, p.Full_Name FROM appointments a JOIN patient p ON a.Patient_NID = p.NID WHERE a.Doctor_NID = '$doctor_nid' AND a.Status = 'Accepted' ORDER BY a.Appointment_Time ASC");

include '../includes/doctor_header.php';
?>

<?php if(!empty($success_msg)) { ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> <?php echo $success_msg; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>

<div class="card mb-4 shadow-sm">
    <div class="card-header bg-warning text-dark fw-bold"><i class="fa-solid fa-clock me-2"></i>Incoming Appointment Requests</div>
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Appointment ID</th>
                    <th>Patient ID</th>
                    <th>Patient Name</th>
                    <th>Reason</th>
                    <th>Assign Serial & Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // 🔥 কলাম সংখ্যা ৬টি হওয়ায় colspan='6' করা হলো
                if(mysqli_num_rows($pending) == 0) {
                    echo "<tr><td colspan='6' class='text-center text-muted py-3'>No new requests</td></tr>";
                }
                while($row = mysqli_fetch_assoc($pending)) { ?>
                <tr>
                    <td><span class="badge bg-secondary">#<?php echo $row['id']; ?></span></td>
                    <td><small class="text-muted"><?php echo htmlspecialchars($row['Patient_NID']); ?></small></td>
                    <td><strong><?php echo htmlspecialchars($row['Full_Name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['Reason']); ?></td>
                    
                    <form method="POST">
                        <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                        <td>
                            <div class="input-group input-group-sm">
                                <input type="number" name="serial_no" class="form-control" placeholder="Serial" required style="max-width:80px;">
                                <input type="datetime-local" name="app_time" class="form-control" required>
                            </div>
                        </td>
                        <td>
                            <button type="submit" name="action" value="accept" class="btn btn-sm btn-success"><i class="fa-solid fa-check"></i> Accept</button>
                        </td>
                    </form>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-success text-white fw-bold"><i class="fa-solid fa-calendar-check me-2"></i>Active View Schedule & Serials</div>
    <div class="card-body table-responsive">
        <table class="table table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>Appointment ID</th>
                    <th>Patient ID</th>
                    <th>Serial</th>
                    <th>Time</th>
                    <th>Patient Name</th>
                    <th>Reason</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // 🔥 কলাম সংখ্যা ৭টি হওয়ায় colspan='7' করা হলো
                if(mysqli_num_rows($accepted) == 0) {
                    echo "<tr><td colspan='7' class='text-center text-muted py-3'>No active schedule for today</td></tr>";
                }
                while($row = mysqli_fetch_assoc($accepted)) { ?>
                <tr>
                    <td><span class="badge bg-secondary">#<?php echo $row['id']; ?></span></td>
                    <td><small class="text-muted"><?php echo htmlspecialchars($row['Patient_NID']); ?></small></td>
                    <td><span class="badge bg-primary fs-6"><?php echo $row['Serial_No']; ?></span></td>
                    <td><i class="fa-regular fa-clock text-danger me-1"></i> <?php echo date('Y-m-d h:i A', strtotime($row['Appointment_Time'])); ?></td>
                    <td><strong><?php echo htmlspecialchars($row['Full_Name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['Reason']); ?></td>
                    <td>
                        <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this appointment?');">
                            <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="action" value="cancel" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-ban"></i> Cancel</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>