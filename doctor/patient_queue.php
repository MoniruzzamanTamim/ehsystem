<?php
session_start();
if (!isset($_SESSION['doctor'])) {
    header("Location: login.php");
    exit();
}
include '../includes/header.php';
?>

<div style="padding: 20px;">
    <h2>Today's Patient Queue</h2>
    <p>The queue automatically refreshes every 5 seconds using AJAX.</p>
    
    <div id="queue_table">
        <p>Loading queue...</p>
    </div>
    
    <br>
    <a href="dashboard.php">Back to Dashboard</a>
</div>

<script src="../js/all_js.js"></script>
<script>
    // পেজ লোড হওয়ামাত্রই প্রথমবার কিউ লোড করার জন্য
    window.onload = loadPatientQueue;
</script>

<?php include '../includes/footer.php'; ?>