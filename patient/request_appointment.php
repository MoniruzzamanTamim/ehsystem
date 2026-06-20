<?php
session_start();
include '../config/dbconnect.php';
if (!isset($_SESSION['nid'])) { header("Location: login.php"); exit(); }
$patient_nid = $_SESSION['nid'];

$doctors = mysqli_query($conn, "SELECT * FROM doctor WHERE Status='Approved'");

if (isset($_POST['submit_request'])) {
    $doctor_nid = $_POST['doctor_nid'];
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);
    
    // ফর্ম থেকে ডেট এবং টাইম আলাদাভাবে নেওয়া হলো
    $input_date = $_POST['appointment_date'];
    $input_time = $_POST['appointment_time'];
    
    // ডেট এবং টাইমকে একত্রে 'YYYY-MM-DD HH:MM:SS' ফরম্যাটে রূপান্তর
    $appointment_datetime = $input_date . ' ' . $input_time . ':00';
    
    // ইনসার্ট কুয়েরি
    $query = "INSERT INTO appointments (Patient_NID, Doctor_NID, Reason, Appointment_Time, Status) 
              VALUES ('$patient_nid', '$doctor_nid', '$reason', '$appointment_datetime', 'Pending')";
              
    if (mysqli_query($conn, $query)) {
        // 🔥 এই ফাংশনটি স্বয়ংক্রিয়ভাবে তৈরি হওয়া নতুন Appointment ID-টি তুলে নিয়ে আসবে
        $inserted_id = mysqli_insert_id($conn);

        // যদি appointments টেবিলে আলাদা Appointment_ID কলাম থাকে, সেটি আপডেট করে নেওয়া হবে
        $update_query = "UPDATE appointments SET Appointment_ID = '$inserted_id' 
                         WHERE Patient_NID = '$patient_nid' 
                           AND Doctor_NID = '$doctor_nid' 
                           AND Appointment_Time = '$appointment_datetime' 
                           AND Status = 'Pending' 
                         ORDER BY Appointment_Time DESC 
                         LIMIT 1";
        mysqli_query($conn, $update_query);

        $msg = "Appointment request submitted successfully! Your Appointment ID is: <strong>#$inserted_id</strong>";
    } else {
        $error_msg = "Error: " . mysqli_error($conn);
    }
}
include __DIR__ . '/../includes/patient_header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card p-4 shadow">
            <div class="card-header bg-primary text-white rounded-top">
                <h4 class="mb-0"><i class="fa-solid fa-calendar-plus me-2"></i>Book An Appointment</h4>
            </div>
            <div class="card-body mt-3">
                <?php 
                // এখানে 'alert-dismissible' ক্লাস যোগ করা হয়েছে যাতে রোগী মেসেজটি ক্রস দিয়ে কেটে দিতে পারে
                if(isset($msg)) echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>$msg<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>"; 
                if(isset($error_msg)) echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>$error_msg<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>"; 
                ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Choose Specialist Doctor</label>
                        <select name="doctor_nid" class="form-select" required>
                            <option value="">-- Select Doctor --</option>
                            <?php while ($doc = mysqli_fetch_assoc($doctors)) {
                                echo "<option value='{$doc['NID']}'>{$doc['Full_Name']} ({$doc['Specialization']})</option>";
                            } ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Appointment Date</label>
                        <input type="date" name="appointment_date" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Appointment Time</label>
                        <input type="time" name="appointment_time" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Reason / Symptoms</label>
                        <textarea name="reason" class="form-control" rows="4" placeholder="Describe your problems..." required></textarea>
                    </div>
                    
                    <button type="submit" name="submit_request" class="btn btn-primary w-100 py-2"><i class="fa-solid fa-paper-plane me-2"></i>Send Request</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

